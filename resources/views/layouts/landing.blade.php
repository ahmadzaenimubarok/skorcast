<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Skor Cast — Aplikasi Turnamen & Skor Badminton Online' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="description" content="{{ $description ?? 'Aplikasi skor badminton untuk turnamen komunitas — peserta daftar lewat link, skor masuk dari HP, bracket tersusun dan maju otomatis. Cukup buka dari browser, tanpa install.' }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#0B0E0C">
    <link rel="canonical" href="{{ $canonical ?? 'https://skorcast.online/' }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .font-display { font-family: 'Archivo Black', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
        .rise { animation: rise .65s cubic-bezier(.22,.9,.32,1) both; }
        @keyframes livepulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
        .live-dot { animation: livepulse 1.5s infinite; }
    </style>
</head>
<body class="font-sans antialiased bg-[#0B0E0C] text-[#F2F6F3] min-h-dvh overflow-x-hidden">
    {{-- NAV --}}
    <x-site-nav />

    {{ $slot }}

    <footer class="max-w-6xl mx-auto px-5 sm:px-8 pb-10 pt-6 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#6B7C72]">
        <span>Skor Cast · Badminton Fun Match</span>
        <span>dibuat oleh Ahmad Zaeni Mubarok</span>
    </footer>

    @livewireScripts
</body>
</html>
