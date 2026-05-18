<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Relasi: satu user bisa kirim banyak pesan
    // hasMany = "user ini PUNYA BANYAK pesan"
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Tambahkan is_online dan last_seen_at ke $fillable
    protected $fillable = [
        'name', 'email', 'password', 'is_online', 'last_seen_at'
    ];
    protected $casts = [
    'email_verified_at' => 'datetime',
    'password'          => 'hashed',
    'last_seen_at'      => 'datetime',   // ← tambahkan ini
    'is_online'         => 'boolean',    // ← tambahkan ini
    ];

}