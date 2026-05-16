<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();                         // kolom id otomatis
            $table->foreignId('sender_id')        // siapa yang kirim
                ->constrained('users')          // merujuk ke tabel users
                ->onDelete('cascade');          // jika user dihapus, pesannya ikut terhapus
            $table->foreignId('receiver_id')      // siapa yang terima
                ->nullable()                    // nullable = bisa kosong (untuk group chat)
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('group_id')         // untuk group chat (Hari 3)
                ->nullable()
                ->constrained('group_rooms')
                ->onDelete('cascade');
            $table->text('body');                 // isi pesan
            $table->enum('type', ['private', 'group'])->default('private'); // jenis pesan
            $table->timestamps();                 // created_at dan updated_at otomatis
        });
    }

};


