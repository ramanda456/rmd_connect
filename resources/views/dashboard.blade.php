@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4> Halo, {{ Auth::user()->name }}!</h4>
    <hr>
    <h5>Daftar Pengguna</h5>
    <div class="list-group" id="user-list">
        @foreach($users as $user)
            <a href="{{ route('chat.private', $user->id) }}"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
               id="user-item-{{ $user->id }}">
                <div>
                    <span>{{ $user->name }}</span>
                    @if($user->last_seen_at)
                        <small class="text-muted ms-2" id="last-seen-{{ $user->id }}">
                            {{ $user->is_online ? '' : 'Terakhir online: ' . $user->last_seen_at->diffForHumans() }}
                        </small>
                    @endif
                </div>
                <span class="badge {{ $user->is_online ? 'bg-success' : 'bg-secondary' }}"
                      id="badge-{{ $user->id }}">
                    {{ $user->is_online ? 'Online' : 'Offline' }}
                </span>
            </a>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>

function startUserStatusListener() {

    console.log('🚀 User status listener aktif');

    // Listen channel realtime
    window.Echo.channel('user-status')
        .listen('.user.status', function (data) {

            console.log('👤 Status berubah:', data);

            const badge = document.getElementById('badge-' + data.userId);
            const lastSeen = document.getElementById('last-seen-' + data.userId);

            if (!badge) return;

            if (data.isOnline) {

                badge.className = 'badge bg-success';
                badge.textContent = 'Online';

                if (lastSeen) {
                    lastSeen.textContent = '';
                }

            } else {

                badge.className = 'badge bg-secondary';
                badge.textContent = 'Offline';

                if (lastSeen) {
                    lastSeen.textContent = 'Baru saja offline';
                }
            }
        });
}


// Tunggu Echo benar-benar connect dulu
if (
    window.Echo &&
    window.Echo.connector &&
    window.Echo.connector.pusher.connection.state === 'connected'
) {

    startUserStatusListener();

} else {

    window.addEventListener('echo-ready', startUserStatusListener);

}

</script>
@endpush