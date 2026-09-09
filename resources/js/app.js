import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Laravel = window.Laravel || {};

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.Laravel.reverbKey || import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.Laravel.reverbHost || import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: parseInt(window.Laravel.reverbPort || import.meta.env.VITE_REVERB_PORT || '8080'),
    wssPort: parseInt(window.Laravel.reverbPort || import.meta.env.VITE_REVERB_PORT || '8080'),
    forceTLS: (window.Laravel.reverbScheme || import.meta.env.VITE_REVERB_SCHEME) === 'https',
    enabledTransports: ['ws', 'wss'],
});
