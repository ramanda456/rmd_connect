<?php
namespace App\Http\Controllers;

use App\Models\GroupRoom;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use App\Events\GroupMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // Halaman daftar group
    public function index()
    {
        // Ambil group yang diikuti user yang sedang login
        $groups = GroupRoom::whereHas('members', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();

        // Ambil semua user untuk form invite member
        $users = User::where('id', '!=', Auth::id())->get();

        return view('group.index', compact('groups', 'users'));
    }

    // Buat group baru
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        // Buat room baru
        $group = GroupRoom::create([
            'name'       => $request->name,
            'created_by' => Auth::id(),
        ]);

        // Tambahkan pembuat sebagai member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
        ]);

        // Tambahkan member yang dipilih
        if ($request->member_ids) {
            foreach ($request->member_ids as $userId) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id'  => $userId,
                ]);
            }
        }

        return redirect()->route('group.chat', $group->id)
                         ->with('success', 'Group berhasil dibuat!');
    }

    // Halaman chat group
    public function chat(GroupRoom $group)
    {
        // Cek apakah user adalah member group ini
        $isMember = GroupMember::where('group_id', $group->id)
                               ->where('user_id', Auth::id())
                               ->exists();

        if (!$isMember) {
            return redirect()->route('group.index')
                             ->with('error', 'Kamu bukan member group ini!');
        }

        $messages = Message::where('group_id', $group->id)
                           ->with('sender')
                           ->orderBy('created_at', 'asc')
                           ->get();

        $members = $group->members;

        return view('group.chat', compact('group', 'messages', 'members'));
    }

    // Kirim pesan ke group
    public function sendMessage(Request $request)
    {
        $request->validate([
            'body'     => 'required|string|max:1000',
            'group_id' => 'required|exists:group_rooms,id',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'group_id'  => $request->group_id,
            'body'      => $request->body,
            'type'      => 'group',
        ]);

        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json([
            'message' => $message->load('sender')
        ]);
    }
}