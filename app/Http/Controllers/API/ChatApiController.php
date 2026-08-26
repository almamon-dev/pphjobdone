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

        // Get ALL other users (admin and non-admin) for search
        $allUsers = \App\Models\User::where('id', '!=', $user->id)
            ->select('id', 'name', 'avatar', 'is_online', 'is_admin')
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
            ->get()
            ->map(function ($msg) {
                $msg->file_url = $msg->file_path ? \App\Helpers\Helper::generateURL($msg->file_path) : null;
                return $msg;
            });

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
            $file_path = \App\Helpers\Helper::uploadFile('chat_files', $file);
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

    public function chatBot(Request $request, \App\Services\OpenAiService $openAiService)
    {
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|string',
        ]);

        $user = $request->user('sanctum') ?: auth('sanctum')->user();
        $sessionId = $request->input('session_id') ?: $request->header('X-Session-ID') ?: ('guest_' . md5($request->ip() . $request->userAgent()));

        $services = \App\Models\Service::with('pricingPlans')->get()->map(function($s) {
            return [
                'name' => $s->title,
                'description' => $s->short_description,
                'plans' => $s->pricingPlans->map(fn($p) => $p->name . ': $' . $p->price)
            ];
        })->toArray();

        $context = [
            'agency_name' => 'Gajura',
            'services' => $services,
            'faq' => 'We offer SEO, Link Building, Content Writing. Payments are secure. Support is 24/7.'
        ];

        // Find or create Lead record
        $lead = null;
        if ($user) {
            $lead = \App\Models\Lead::where('user_id', $user->id)->first();
        }
        if (!$lead && $sessionId) {
            $lead = \App\Models\Lead::where('session_id', $sessionId)->first();
        }

        $userName = $user ? $user->name : ($request->input('user_name') ?: null);
        $userEmail = $user ? $user->email : ($request->input('user_email') ?: null);

        if (!$lead) {
            $lead = new \App\Models\Lead([
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'name' => $userName,
                'email' => $userEmail,
                'qualification_status' => 'Cold',
                'chat_history' => [],
            ]);
        } else {
            if (!$lead->user_id && $user) {
                $lead->user_id = $user->id;
            }
            if (!$lead->name && $userName) {
                $lead->name = $userName;
            }
            if (!$lead->email && $userEmail) {
                $lead->email = $userEmail;
            }
        }

        $chatHistory = $lead->chat_history ?: [];
        $chatHistory[] = [
            'sender' => 'user',
            'message' => $request->message,
            'timestamp' => now()->toDateTimeString(),
        ];

        $aiResult = $openAiService->generateChatbotReply($request->message, $context);
        $replyText = is_array($aiResult) ? ($aiResult['reply'] ?? 'How can I assist you?') : $aiResult;
        $leadInfo = is_array($aiResult) ? ($aiResult['lead_info'] ?? []) : [];

        $chatHistory[] = [
            'sender' => 'bot',
            'message' => $replyText,
            'timestamp' => now()->toDateTimeString(),
        ];

        $lead->chat_history = $chatHistory;

        $wasHot = $lead->qualification_status === 'Hot';

        // Update extracted lead fields if present
        if (!empty($leadInfo['name'])) $lead->name = $leadInfo['name'];
        if (!empty($leadInfo['email'])) $lead->email = $leadInfo['email'];
        if (!empty($leadInfo['phone'])) $lead->phone = $leadInfo['phone'];
        if (!empty($leadInfo['company_name'])) $lead->company_name = $leadInfo['company_name'];
        if (!empty($leadInfo['service_interest'])) $lead->service_interest = $leadInfo['service_interest'];
        if (!empty($leadInfo['budget'])) $lead->budget = $leadInfo['budget'];
        if (!empty($leadInfo['qualification_status'])) $lead->qualification_status = $leadInfo['qualification_status'];
        if (!empty($leadInfo['qualification_summary'])) $lead->qualification_summary = $leadInfo['qualification_summary'];

        $lead->save();

        // Send instant Admin Email Alert if lead becomes Hot
        if (!$wasHot && $lead->qualification_status === 'Hot') {
            try {
                $adminEmail = config('mail.from.address') ?: 'admin@pphjobdone.com';
                Mail::to($adminEmail)->send(new \App\Mail\HotLeadAlertMail($lead));
            } catch (\Exception $e) {
                Log::error('Failed to send Hot Lead Alert mail: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => $replyText,
                'sender' => 'AI Assistant',
                'lead_id' => $lead->id,
                'qualification_status' => $lead->qualification_status,
                'created_at' => now()
            ]
        ]);
    }

    public function updateMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        $message->update([
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $message,
        ]);
    }

    public function deleteMessage($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        if ($message->file_path) {
            \App\Helpers\Helper::deleteFile($message->file_path);
        }

        $message->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Message deleted successfully',
        ]);
    }
}
