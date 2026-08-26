<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminMessageController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth()->user();

        // Get conversations involving admin or general client-to-admin chats
        $conversations = Conversation::with(['sender', 'receiver', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])
        ->orderBy('last_message_at', 'desc')
        ->get()
        ->map(function ($convo) use ($admin) {
            $otherUser = $convo->sender_id === $admin->id ? $convo->receiver : $convo->sender;

            return [
                'id' => $convo->id,
                'other_user' => [
                    'id' => $otherUser->id ?? null,
                    'name' => $otherUser->name ?? 'Client',
                    'email' => $otherUser->email ?? '',
                    'avatar' => $otherUser->avatar ?? null,
                    'is_online' => $otherUser->is_online ?? false,
                ],
                'last_message' => $convo->messages->first() ? [
                    'id' => $convo->messages->first()->id,
                    'message' => $convo->messages->first()->message,
                    'type' => $convo->messages->first()->type,
                    'created_at' => $convo->messages->first()->created_at->diffForHumans(),
                ] : null,
                'last_message_at' => $convo->last_message_at,
                'unread_count' => Message::where('conversation_id', $convo->id)
                    ->where('receiver_id', $admin->id)
                    ->where('is_read', false)
                    ->count(),
            ];
        });

        // Determine active conversation or active user to chat with
        $activeConversationId = $request->query('conversation_id');
        $selectedUserId = $request->query('user_id');

        if (!$activeConversationId && $selectedUserId) {
            $convo = Conversation::where(function ($q) use ($admin, $selectedUserId) {
                $q->where('sender_id', $admin->id)->where('receiver_id', $selectedUserId);
            })->orWhere(function ($q) use ($admin, $selectedUserId) {
                $q->where('sender_id', $selectedUserId)->where('receiver_id', $admin->id);
            })->first();

            if ($convo) {
                $activeConversationId = $convo->id;
            } else {
                // Pre-create conversation with client
                $convo = Conversation::create([
                    'sender_id' => $admin->id,
                    'receiver_id' => $selectedUserId,
                    'last_message_at' => now(),
                ]);
                $activeConversationId = $convo->id;
            }
        }

        if (!$activeConversationId && $conversations->isNotEmpty()) {
            $activeConversationId = $conversations->first()['id'];
        }

        $activeMessages = [];
        $activeClient = null;

        if ($activeConversationId) {
            $convoModel = Conversation::with(['sender', 'receiver'])->find($activeConversationId);
            if ($convoModel) {
                $activeClient = $convoModel->sender_id === $admin->id ? $convoModel->receiver : $convoModel->sender;

                $activeMessages = Message::where('conversation_id', $activeConversationId)
                    ->with('sender')
                    ->oldest()
                    ->get()
                    ->map(function ($msg) {
                        return [
                            'id' => $msg->id,
                            'sender_id' => $msg->sender_id,
                            'message' => $msg->message,
                            'type' => $msg->type,
                            'file_path' => $msg->file_path ? \App\Helpers\Helper::generateURL($msg->file_path) : null,
                            'created_at' => $msg->created_at->format('h:i A, M d'),
                            'is_me' => $msg->sender_id === auth()->id(),
                        ];
                    });

                // Mark messages as read for admin
                Message::where('conversation_id', $activeConversationId)
                    ->where('receiver_id', $admin->id)
                    ->update(['is_read' => true]);
            }
        }

        $clients = User::where('id', '!=', $admin->id)
            ->select('id', 'name', 'email', 'avatar')
            ->get();

        return Inertia::render('Admin/Messages/Index', [
            'conversations' => $conversations,
            'activeConversationId' => (int)$activeConversationId,
            'activeMessages' => $activeMessages,
            'activeClient' => $activeClient,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,zip|max:5120',
        ]);

        $adminId = auth()->id();
        $receiverId = $request->receiver_id;

        $conversation = null;
        if ($request->conversation_id) {
            $conversation = Conversation::find($request->conversation_id);
        }

        if (!$conversation) {
            $conversation = Conversation::where(function ($q) use ($adminId, $receiverId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($adminId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $adminId);
            })->first();
        }

        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_id' => $adminId,
                'receiver_id' => $receiverId,
                'last_message_at' => now(),
            ]);
        }

        $type = 'text';
        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = \App\Helpers\Helper::uploadFile('chat_files', $file);
            $extension = strtolower($file->getClientOriginalExtension());
            $type = in_array($extension, ['jpeg', 'png', 'jpg', 'gif', 'svg']) ? 'image' : 'file';
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $adminId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'type' => $type,
            'file_path' => $filePath,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Smart Lead Automation: Auto-update lead status from 'new' to 'contacted'
        $receiverUser = User::find($receiverId);
        if ($receiverUser) {
            \App\Models\Lead::where(function ($q) use ($receiverUser) {
                $q->where('user_id', $receiverUser->id)
                  ->orWhere('email', $receiverUser->email);
            })->where('status', 'new')
              ->update(['status' => 'contacted']);
        }

        return redirect()->back();
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::findOrFail($id);

        if ($message->sender_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $message->update([
            'message' => $request->message,
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        if ($message->sender_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        if ($message->file_path) {
            \App\Helpers\Helper::deleteFile($message->file_path);
        }

        $message->delete();

        return redirect()->back();
    }
}
