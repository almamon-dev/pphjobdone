<?php

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = User::first();
$receiver = User::skip(1)->first();

if (!$user || !$receiver) {
    die("Need at least two users to test. Please run migrations and seeders if needed.\n");
}

$conv = Conversation::updateOrCreate(
    ['sender_id' => $user->id, 'receiver_id' => $receiver->id],
    ['last_message_at' => now()]
);

$msg = Message::create([
    'conversation_id' => $conv->id,
    'sender_id' => $user->id,
    'receiver_id' => $receiver->id,
    'message' => 'Reverb real-time test at ' . now(),
]);

echo "Broadcasting message ID: " . $msg->id . "\n";

try {
    broadcast(new MessageSent($msg));
    echo "Message broadcast successfully via " . config('broadcasting.default') . "!\n";
} catch (\Exception $e) {
    echo "Broadcast failed: " . $e->getMessage() . "\n";
}
