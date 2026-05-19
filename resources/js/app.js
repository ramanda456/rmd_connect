import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'vspako5uwf28rmcvr01y',
    wsHost: 'localhost',
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
        }
    }
});

window.Echo.connector.pusher.connection.bind('connected', function () {
    console.log(' Reverb WebSocket TERHUBUNG!');
    window.dispatchEvent(new Event('echo-ready'));
});

window.Echo.connector.pusher.connection.bind('error', function (err) {
    console.error(' Reverb error:', err);
});