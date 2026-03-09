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

        return response()->json([
            'status' => 'success',
            'data' => $conversations
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
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // Log or handle error if pusher settings are not complete
        }

        return response()->json([
            'status' => 'success',
            'data' => $message->load(['sender', 'receiver'])
        ]);
    }
}
