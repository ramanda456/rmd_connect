<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn(): array
    {
        return [new Channel('group.' . $this->message->group_id)];
    }

    public function broadcastAs(): string
    {
        return 'group.message';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'        => $this->message->id,
                'body'      => $this->message->body,
                'sender_id' => $this->message->sender_id,
                'group_id'  => $this->message->group_id,
                'sender'    => [
                    'id'   => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                ],
            ],
        ];
    }
}