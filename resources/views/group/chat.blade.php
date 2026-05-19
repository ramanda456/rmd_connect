@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 750px;">
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('group.index') }}" class="btn btn-sm btn-outline-secondary">←</a>
                <strong>👥 {{ $group->name }}</strong>
            </div>
            <small class="text-muted">{{ $members->count() }} member</small>
        </div>

        {{-- Daftar member --}}
        <div class="px-3 pt-2 pb-1 border-bottom bg-light">
            <small class="text-muted">
                Member:
                @foreach($members as $member)
                    <span class="badge bg-secondary me-1">{{ $member->name }}</span>
                @endforeach
            </small>
        </div>

        {{-- Area pesan --}}
        <div id="messages" class="card-body"
             style="height:400px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
            @foreach($messages as $msg)
                <div class="d-flex {{ $msg->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="rounded p-2 px-3 {{ $msg->sender_id === Auth::id() ? 'bg-primary text-white' : 'bg-light border' }}"
                         style="max-width:70%;">
                        <small class="d-block fw-bold">{{ $msg->sender->name }}</small>
                        {{ $msg->body }}
                        <small class="d-block text-end opacity-75" style="font-size:0.7rem">
                            {{ $msg->created_at->format('H:i') }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input kirim pesan --}}
        <div class="card-footer">
            <div class="input-group">
                <input type="text" id="message-input" class="form-control"
                       placeholder="Ketik pesan..." autocomplete="off">
                <button id="send-btn" class="btn btn-primary">Kirim</button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const GROUP_ID      = parseInt("{{ $group->id }}");
const GROUP_USER_ID = parseInt("{{ Auth::id() }}");
const GROUP_URL     = "{{ route('group.send') }}";
const GROUP_CSRF    = document.querySelector('meta[name="csrf-token"]').content;

// Scroll ke bawah
document.getElementById('messages').scrollTop =
    document.getElementById('messages').scrollHeight;

function groupAppendMessage(body, senderName, isMine) {
    const box = document.getElementById('messages');
    const div = document.createElement('div');
    div.className = 'd-flex ' + (isMine ? 'justify-content-end' : 'justify-content-start');
    div.innerHTML = `
        <div class="rounded p-2 px-3 ${isMine ? 'bg-primary text-white' : 'bg-light border'}"
             style="max-width:70%; margin-bottom:4px;">
            <small class="d-block fw-bold">${senderName}</small>
            <span>${body}</span>
            <small class="d-block text-end opacity-75" style="font-size:.7rem">
                ${new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})}
            </small>
        </div>`;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

document.getElementById('send-btn').addEventListener('click', function () {
    const input = document.getElementById('message-input');
    const body  = input.value.trim();
    if (!body) return;
    this.disabled = true;

    fetch(GROUP_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': GROUP_CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ body: body, group_id: GROUP_ID })
    })
    .then(res => res.json())
    .then(data => {
        groupAppendMessage(data.message.body, 'Anda', true);
        input.value = '';
        input.focus();
    })
    .catch(err => console.error('❌ Gagal kirim:', err))
    .finally(() => { this.disabled = false; });
});

document.getElementById('message-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') document.getElementById('send-btn').click();
});

// Listen WebSocket group — channel publik (tidak perlu auth)
function startGroupListening() {
    const chan = `group.${GROUP_ID}`;
    console.log('📡 Listening ke group channel:', chan);

    window.Echo.channel(chan)
        .listen('.group.message', function (data) {
            console.log('📨 Pesan group masuk:', data);
            if (data.message.sender_id !== GROUP_USER_ID) {
                groupAppendMessage(data.message.body, data.message.sender.name, false);
            }
        });
}

if (window.Echo && window.Echo.connector.pusher.connection.state === 'connected') {
    startGroupListening();
} else {
    window.addEventListener('echo-ready', startGroupListening);
}
</script>
@endpush