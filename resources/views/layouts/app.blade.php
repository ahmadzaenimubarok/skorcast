<!DOCTYPE html>
<html lang="id" class="bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Skor Cast — {{ $title ?? 'Admin' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-100">
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('tournaments.index') }}" class="text-xl font-bold tracking-tight">
                        🏸 Skor Cast
                    </a>
                    <span class="text-sm text-gray-400">Admin</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <span class="hidden sm:inline">skorcast.online</span>
                    @auth
                        <span class="text-gray-500 hidden sm:inline">·</span>
                        <form method="POST" action="{{ url('/logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors">Keluar</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @livewireScripts

    <div class="text-center text-xs text-gray-700 py-6 border-t border-gray-800 mt-8">
        <a href="https://github.com/ahmadzaenimubarok/skorcast" target="_blank" class="hover:text-gray-500 transition-colors">🐙 GitHub</a>
        &middot; Skor Cast &middot; skorcast.online
    </div>
</body>
</html>
