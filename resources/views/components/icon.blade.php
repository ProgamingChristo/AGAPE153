@props([
    'name',
    'class' => 'h-5 w-5',
])

@php
    $paths = [
        'cart' => '<path d="M6 6h15l-2 9H8L6 6Z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>',
        'logout' => '<path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>',
        'login' => '<path d="M14 17l5-5-5-5"/><path d="M19 12H7"/><path d="M10 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>',
        'dashboard' => '<rect x="3" y="3" width="7" height="8" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="15" width="7" height="6" rx="1"/>',
        'orders' => '<path d="M9 3h6"/><path d="M10 9h4"/><path d="M8 13h8"/><path d="M8 17h5"/><rect x="5" y="5" width="14" height="16" rx="2"/>',
        'chart' => '<path d="M4 19V5"/><path d="M4 19h16"/><rect x="7" y="11" width="3" height="5" rx="1"/><rect x="12" y="7" width="3" height="9" rx="1"/><rect x="17" y="9" width="3" height="7" rx="1"/>',
        'settings' => '<path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V22a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7.1 4l.1.1a1.7 1.7 0 0 0 1.9.3h.1a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 20 7.1l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 0 20"/><path d="M12 2a15.3 15.3 0 0 0 0 20"/>',
        'package' => '<path d="m7.5 4.3 9 5.2"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/><path d="M21 8v8a2 2 0 0 1-1 1.7l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 16V8a2 2 0 0 1 1-1.7l7-4a2 2 0 0 1 2 0l7 4A2 2 0 0 1 21 8Z"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'truck' => '<path d="M10 17H6a2 2 0 0 1-2-2V5h11v12"/><path d="M15 8h4l3 4v3a2 2 0 0 1-2 2h-1"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v5l3 2"/>',
        'at' => '<circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/>',
        'music' => '<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>',
        'youtube' => '<rect x="3" y="7" width="18" height="10" rx="3"/><path d="m10 10 5 2-5 2Z"/>',
        'instagram' => '<rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.2"/><path d="M16.5 7.5h.01"/>',
        'facebook' => '<path d="M14 8h2V4h-2a5 5 0 0 0-5 5v2H7v4h2v5h4v-5h3l1-4h-4V9a1 1 0 0 1 1-1Z"/>',
        'linkedin' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 11v5"/><path d="M8 8h.01"/><path d="M12 16v-5"/><path d="M12 13a2 2 0 0 1 4 0v3"/>',
        'tiktok' => '<path d="M14 4v9.2a4 4 0 1 1-4-4"/><path d="M14 4c.7 2.6 2.4 4.2 5 4.7"/>',
        'threads' => '<path d="M17.5 8.6c-.9-2.1-2.8-3.3-5.2-3.3-3.5 0-6 2.7-6 6.7s2.5 6.7 6 6.7c3.1 0 5.5-1.9 5.5-4.5 0-2.2-1.6-3.6-4.8-4"/><path d="M9.4 13.8c0 1.4 1.1 2.3 2.8 2.3s2.9-.9 2.9-2.2c0-1.4-1.1-2.2-2.9-2.2-1.7 0-2.8.8-2.8 2.1Z"/>',
        'home' => '<path d="m3 10 9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
        'download' => '<path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/>',
        'upload' => '<path d="M12 21V9"/><path d="m17 14-5-5-5 5"/><path d="M5 3h14"/>',
        'message' => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/>',
        'video' => '<path d="M15 10l5-3v10l-5-3Z"/><rect x="3" y="6" width="12" height="12" rx="2"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'leaf' => '<path d="M11 20A7 7 0 0 1 4 13c0-5 7-9 16-9 0 9-4 16-9 16Z"/><path d="M4 13c4 0 8 0 12-8"/>',
        'coffee' => '<path d="M10 2v2"/><path d="M14 2v2"/><path d="M16 8H8v7a4 4 0 0 0 4 4 4 4 0 0 0 4-4Z"/><path d="M16 10h2a3 3 0 0 1 0 6h-2"/><path d="M6 22h12"/>',
        'language' => '<path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/><circle cx="12" cy="12" r="10"/>',
        'arrow' => '<path d="M5 12h14"/><path d="m13 5 7 7-7 7"/>',
        'map' => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'lock' => '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'menu' => '<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>',
        'x' => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'trash' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/>',
        'print' => '<path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/>',
        'star' => '<path d="m12 2 3 6 6 .9-4.5 4.4 1.1 6.2L12 16.6 6.4 19.5l1.1-6.2L3 8.9 9 8z"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths[$name] ?? $paths['globe'] !!}
</svg>
