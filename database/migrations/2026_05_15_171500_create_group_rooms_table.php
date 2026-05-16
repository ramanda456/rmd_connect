<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('group_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // nama group
            $table->foreignId('created_by')       // siapa yang buat group
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

};
