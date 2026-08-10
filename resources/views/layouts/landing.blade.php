<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Skor Cast — Aplikasi Turnamen & Skor Badminton Online' }}</title>
    <meta name="description" content="{{ $description ?? 'Aplikasi skor badminton untuk turnamen komunitas — peserta daftar lewat link, skor masuk dari HP, bracket tersusun dan maju otomatis. Tanpa aplikasi, tanpa akun.' }}">
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
    <header class="max-w-6xl mx-auto px-5 sm:px-8 pt-6 flex items-center justify-between">
        <a href="/" class="font-display text-sm tracking-[0.18em] text-[#F2F6F3] no-underline">SKOR<span class="text-[#4ADE80]">CAST</span></a>
        <nav class="flex items-center gap-1 sm:gap-2">
            <a href="/s" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Scoreboard</a>
            <a href="/turnamen" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Turnamen</a>
        </nav>
    </header>

    {{ $slot }}

    <footer class="max-w-6xl mx-auto px-5 sm:px-8 pb-10 pt-6 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#6B7C72]">
        <span>Skor Cast · Badminton Fun Match</span>
        <span>dibuat oleh Ahmad Zaeni Mubarok</span>
    </footer>

    @livewireScripts
</body>
</html>
