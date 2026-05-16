<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Dashboard
    public function index()
    {
        // Ambil semua user kecuali diri sendiri
        $users = User::where('id', '!=', Auth::id())->get();

        return view('dashboard', compact('users'));
    }

    // Halaman private chat
    public function privateChat(User $user)
    {
        // Ambil riwayat chat antara user login dan user tujuan
        $messages = Message::where('type', 'private')

            ->where(function ($query) use ($user) {

                // Pesan dari saya ke user
                $query->where('sender_id', Auth::id())
                    ->where('receiver_id', $user->id);

            })

            ->orWhere(function ($query) use ($user) {

                // Pesan dari user ke saya
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', Auth::id());

            })

            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.private', compact('user', 'messages'));
    }

    // Kirim pesan
    public function sendMessage(Request $request)
    {
        // Validasi input
        $request->validate([
            'body' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        // Simpan pesan ke database
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
            'type' => 'private',
        ]);

        // Broadcast realtime
        broadcast(new MessageSent($message))->toOthers();

        // Return JSON
        return response()->json([
            'message' => $message->load('sender')
        ]);
    }
}