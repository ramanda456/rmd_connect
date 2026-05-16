@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 700px;">
    <div class="card">

        {{-- Header chat --}}
        <div class="card-header d-flex align-items-center gap-2">
            <a href="{{ route('dashboard') }}"
               class="btn btn-sm btn-outline-secondary">
                ←
            </a>

            <strong>{{ $user->name }}</strong>

            <span id="status-badge"
                  class="badge {{ $user->is_online ? 'bg-success' : 'bg-secondary' }}">
                {{ $user->is_online ? 'Online' : 'Offline' }}
            </span>
        </div>

        {{-- Area pesan --}}
        <div id="messages"
             class="card-body"
             style="height: 400px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">

            @foreach($messages as $msg)

                <div class="d-flex {{ $msg->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">

                    <div class="rounded p-2 px-3
                        {{ $msg->sender_id === Auth::id()
                            ? 'bg-primary text-white'
                            : 'bg-light'
                        }}"
                        style="max-width:70%;">

                        <small class="d-block fw-bold">
                            {{ $msg->sender->name }}
                        </small>

                        {{ $msg->body }}

                        <small class="d-block text-end opacity-75"
                               style="font-size:0.7rem">

                            {{ $msg->created_at->format('H:i') }}

                        </small>
                    </div>
                </div>

            @endforeach
        </div>

        {{-- Input kirim pesan --}}
        <div class="card-footer">

            <div class="input-group">

                <input
                    type="text"
                    id="message-input"
                    class="form-control"
                    placeholder="Ketik pesan..."
                    autocomplete="off"
                >

                <button id="send-btn"
                        class="btn btn-primary">
                    Kirim
                </button>

            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')

{{-- Data untuk JavaScript --}}
<div id="chat-data"
     data-receiver-id="{{ $user->id }}"
     data-auth-user-id="{{ Auth::id() }}"
     data-send-url="{{ route('chat.send') }}">
</div>


<script>

const chatData = document.getElementById('chat-data');

const RECEIVER_ID = chatData.dataset.receiverId;
const AUTH_USER_ID = chatData.dataset.authUserId;
const SEND_URL = chatData.dataset.sendUrl;

const CSRF_TOKEN =
    document.querySelector('meta[name="csrf-token"]').content;


function appendMessage(body, senderName, isMine, time)
{
    const messages = document.getElementById('messages');

    const div = document.createElement('div');

    div.className =
        'd-flex ' +
        (isMine ? 'justify-content-end'
                : 'justify-content-start');

    div.innerHTML = `
        <div class="rounded p-2 px-3
            ${isMine ? 'bg-primary text-white'
                     : 'bg-light'}"
            style="max-width:70%">

            <small class="d-block fw-bold">
                ${senderName}
            </small>

            ${body}

            <small class="d-block text-end opacity-75"
                   style="font-size:.7rem">

                ${time}

            </small>
        </div>
    `;

    messages.appendChild(div);

    messages.scrollTop = messages.scrollHeight;
}


// Kirim pesan
document.getElementById('send-btn')
.addEventListener('click', function() {

    const input = document.getElementById('message-input');

    const body = input.value.trim();

    if (!body) return;


    fetch(SEND_URL, {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },

        body: JSON.stringify({
            body,
            receiver_id: RECEIVER_ID
        })

    })

    .then(res => res.json())

    .then(data => {

        appendMessage(
            data.message.body,
            'Anda',
            true,
            new Date().toLocaleTimeString(
                'id-ID',
                {
                    hour:'2-digit',
                    minute:'2-digit'
                }
            )
        );

        input.value = '';
    });
});


// Enter untuk kirim
document.getElementById('message-input')
.addEventListener('keypress', function(e) {

    if (e.key === 'Enter') {

        document.getElementById('send-btn').click();
    }
});


// Realtime listener
const ids =
    [AUTH_USER_ID, RECEIVER_ID]
    .sort((a,b) => a - b);

const channelName =
    `chat.${ids[0]}.${ids[1]}`;


window.Echo.private(channelName)

.listen('.message.sent', (data) => {

    const isMine =
        data.message.sender_id == AUTH_USER_ID;

    if (!isMine) {

        appendMessage(

            data.message.body,

            data.message.sender.name,

            false,

            new Date().toLocaleTimeString(
                'id-ID',
                {
                    hour:'2-digit',
                    minute:'2-digit'
                }
            )
        );
    }
});

</script>

@endpush