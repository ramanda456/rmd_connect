<?php
use Illuminate\Support\Facades\Broadcast;

// Private channel: cek apakah user boleh listen ke channel ini
Broadcast::channel('chat.{id1}.{id2}', function ($user, $id1, $id2) {
    // User hanya boleh masuk kalau ID-nya ada di channel tersebut
    return (int) $user->id === (int) $id1 || (int) $user->id === (int) $id2;
});

