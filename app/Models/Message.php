<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // $fillable = kolom yang boleh diisi lewat kode
    protected $fillable = ['sender_id', 'receiver_id', 'group_id', 'body', 'type'];

    // Relasi: pesan ini dikirim oleh user mana
    // belongsTo = "pesan ini MILIK satu user sebagai pengirim"
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relasi: pesan ini diterima oleh user mana
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}