<header class="max-w-6xl mx-auto px-5 sm:px-8 pt-6 flex items-center justify-between">
    <a href="/" class="font-display text-lg tracking-[0.12em] text-[#F2F6F3] no-underline">SKOR<span class="text-[#4ADE80]">CAST</span></a>
    <nav class="flex items-center gap-1 sm:gap-2">
        <a href="/s" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Scoreboard</a>
        <a href="/turnamen" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Turnamen</a>
        @auth
            <a href="{{ auth()->user()->role === 'admin' ? '/admin' : '/wasit' }}" class="px-3 py-2 rounded-lg text-sm font-medium text-[#4ADE80] bg-[#4ADE80]/10 hover:bg-[#4ADE80]/20 transition-colors no-underline">Dashboard</a>
        @else
            <a href="/login" class="px-3 py-2 rounded-lg text-sm font-medium text-[#0B0E0C] bg-[#4ADE80] hover:bg-[#3DCA72] transition-colors no-underline">Login</a>
        @endauth
    </nav>
</header>
