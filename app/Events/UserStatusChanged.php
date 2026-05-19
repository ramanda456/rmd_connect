<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class UserStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public int $userId;
    public bool $isOnline;

    public function __construct(User $user)
    {
        $this->userId   = $user->id;
        $this->isOnline = $user->is_online;
    }

    public function broadcastOn(): array
    {
        // Channel publik — semua user bisa dengar status orang lain
        return [new Channel('user-status')];
    }

    public function broadcastAs(): string
    {
        return 'user.status';
    }
}