<!DOCTYPE html>
<html lang="id" class="bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Skor Cast — Turnamen & Skor Badminton' }}</title>
    <meta name="description" content="{{ $description ?? 'Skor Cast — aplikasi turnamen & skor badminton untuk komunitas. Peserta daftar lewat link, skor masuk dari HP, bracket tersusun otomatis.' }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .polling-indicator { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="font-sans antialiased text-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-6">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>