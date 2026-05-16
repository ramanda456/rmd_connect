<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

// ShouldBroadcast = interface yang menandakan event ini akan dikirim ke WebSocket
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    // public property otomatis dikirim sebagai data ke client
    public Message $message;

    public function __construct(Message $message)
    {
        // Load relasi sender agar nama pengirim ikut terkirim
        $this->message = $message->load('sender');
    }

    // broadcastOn = tentukan channel mana yang akan menerima event ini
    public function broadcastOn(): array
    {
        // PrivateChannel = hanya user yang authorized yang bisa dengar
        // Format channel: "chat.1.2" artinya chat antara user ID 1 dan 2
        $ids = [$this->message->sender_id, $this->message->receiver_id];
        sort($ids); // sort supaya "chat.1.2" dan "chat.2.1" jadi sama

        return [
            new PrivateChannel('chat.' . $ids[0] . '.' . $ids[1]),
        ];
    }

    // Nama event yang diterima oleh JavaScript
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}