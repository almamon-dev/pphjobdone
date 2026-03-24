<?php

namespace App\Http\Controllers\API;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatApiController extends Controller
{
    public function getConversations()
    {
        $user = Auth::user();
        if (!$user) {
             return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $conversations = Conversation::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Map conversations to identify the "other" user clearly and clean up the response
        $conversations = $conversations->map(function ($convo) use ($user) {
            $other = $convo->sender_id === $user->id ? $convo->receiver : $convo->sender;
            
            return [
                'id' => $convo->id,
                'other_user' => [
                    'id' => $other->id,
                    'name' => $other->name,
                    'avatar' => $other->avatar,
                    'is_online' => $other->is_online,
                    'is_admin' => $other->is_admin,
                ],
                'last_message' => $convo->messages->first() ? [
                    'id' => $convo->messages->first()->id,
                    'message' => $convo->messages->first()->message,
                    'type' => $convo->messages->first()->type,
                    'created_at' => $convo->messages->first()->created_at,
                ] : null,
                'last_message_at' => $convo->last_message_at,
                'unread_count' => Message::where('conversation_id', $convo->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count(),
            ];
        });

        // Get IDs of users who already have a conversation with the current user
        $existingConversationUserIds = Conversation::where('sender_id', $user->id)->pluck('receiver_id')
            ->merge(Conversation::where('receiver_id', $user->id)->pluck('sender_id'))
            ->unique()
            ->toArray();

        // Get all other regular users (EXCLUDING Admins and those who are already in recent chats)
        $allUsers = \App\Models\User::where('id', '!=', $user->id)
            ->where('is_admin', false) // Exclude admins
            ->whereNotIn('id', $existingConversationUserIds)
            ->select('id', 'name', 'avatar', 'is_online')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $conversations,
            'all_users' => $allUsers
        ]);
    }

    public function getMessages($conversationId)
    {
        $user = Auth::user();
        if (!$user) {
             return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->with(['sender', 'receiver'])
            ->oldest()
            ->get();

        // Mark as read
        Message::where('conversation_id', $conversationId)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,zip|max:5120',
        ]);

        $sender_id = Auth::id();
        $receiver_id = $request->receiver_id;

        if ($sender_id == $receiver_id) {
            return response()->json(['status' => 'error', 'message' => 'You cannot message yourself'], 400);
        }

        // Find or create conversation
        $conversation = Conversation::where(function ($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $sender_id)->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', $sender_id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'last_message_at' => now(),
            ]);
        }

        $type = 'text';
        $file_path = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file_path = $file->store('chat_files', 'public');
            $extension = $file->getClientOriginalExtension();
            if (in_array($extension, ['jpeg','png','jpg','gif','svg'])) {
                $type = 'image';
            } else {
                $type = 'file';
            }
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $request->message,
            'type' => $type,
            'file_path' => $file_path,
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            \Illuminate\Support\Facades\Log::info('Broadcasting MessageSent event', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id
            ]);
            broadcast(new MessageSent($message))->toOthers();
            \Illuminate\Support\Facades\Log::info('MessageSent event broadcasted successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Broadcasting failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'data' => $message->load(['sender', 'receiver'])
        ]);
    }
}
