@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 700px;">
    <div class="card">

        <div class="card-header d-flex align-items-center gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">←</a>

            <strong>{{ $user->name }}</strong>

            <span
                id="chat-user-status"
                class="badge {{ $user->is_online ? 'bg-success' : 'bg-secondary' }}">
                {{ $user->is_online ? 'Online' : 'Offline' }}
            </span>
        </div>


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
const CHAT_RECEIVER_ID  = parseInt("{{ $user->id }}");
const CHAT_AUTH_USER_ID = parseInt("{{ Auth::id() }}");
const CHAT_SEND_URL     = "{{ route('chat.send') }}";
const CHAT_CSRF         = document.querySelector('meta[name="csrf-token"]').content;

document.getElementById('messages').scrollTop =
    document.getElementById('messages').scrollHeight;

function chatAppendMessage(body, senderName, isMine) {
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

    fetch(CHAT_SEND_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CHAT_CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ body: body, receiver_id: CHAT_RECEIVER_ID })
    })
    .then(res => res.json())
    .then(data => {
        chatAppendMessage(data.message.body, 'Anda', true);
        input.value = '';
        input.focus();
    })
    .catch(err => console.error('❌ Gagal kirim:', err))
    .finally(() => { this.disabled = false; });
});

document.getElementById('message-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') document.getElementById('send-btn').click();
});

function startListening() {
    const ids  = [CHAT_AUTH_USER_ID, CHAT_RECEIVER_ID].sort((a, b) => a - b);
    const chan  = `chat.${ids[0]}.${ids[1]}`;
    console.log('📡 Listening ke channel:', chan);

    window.Echo.private(chan)
        .listen('.message.sent', function (data) {
            console.log('📨 Pesan masuk:', data);
            if (data.message.sender_id !== CHAT_AUTH_USER_ID) {
                chatAppendMessage(data.message.body, data.message.sender.name, false);
            }
        });
}

// Echo dari app.js mungkin belum connected saat script ini jalan
// Tunggu event 'echo-ready' yang di-dispatch dari app.js
if (window.Echo && window.Echo.connector.pusher.connection.state === 'connected') {
    startListening();
} else {
    window.addEventListener('echo-ready', startListening);
}



// LISTEN STATUS ONLINE / OFFLINE REALTIME


window.Echo.channel('presence')
    .listen('.user.status', function (data) {

        console.log('👤 Status user berubah:', data);

        // hanya update badge user yang sedang dibuka chatnya
        if (parseInt(data.userId) === CHAT_RECEIVER_ID) {

            const badge = document.getElementById('chat-user-status');

            if (data.isOnline) {
                badge.className = 'badge bg-success';
                badge.textContent = 'Online';
            } else {
                badge.className = 'badge bg-secondary';
                badge.textContent = 'Offline';
            }
        }
    });

</script>
@endpush