<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard utama
    Route::get('/dashboard', [ChatController::class, 'index'])
        ->name('dashboard');

    // Private chat
    Route::get('/chat/{user}', [ChatController::class, 'privateChat'])
        ->name('chat.private');

    // Kirim pesan
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])
        ->name('chat.send');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';