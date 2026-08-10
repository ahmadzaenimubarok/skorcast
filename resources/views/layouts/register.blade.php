<!DOCTYPE html>
<html lang="id" class="bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>🏸 Pendaftaran — Badminton Fun Match</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-100 min-h-screen bg-gray-950">
    <div class="max-w-xl mx-auto px-4 py-8">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
