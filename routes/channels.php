<?php

use Illuminate\Support\Facades\Broadcast;

// Private channel chat
Broadcast::channel('chat.{id1}.{id2}', function ($user, $id1, $id2) {

    return (int) $user->id === (int) $id1
        || (int) $user->id === (int) $id2;
});