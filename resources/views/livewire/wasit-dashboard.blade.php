{{--
PANEL WASIT: daftar turnamen untuk wasit.
TANPA tombol buat/edit/hapus. Tanpa link /admin.
Akses: hanya lihat + masuk bracket untuk input skor (tombol Lihat Bracket -> /w/{code}).
--}}

<div>
    {{-- HEADER WASIT --}}
    <header class="border-b border-white/5">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">🏸</span>
                <span class="font-display font-bold tracking-tight text-[#F2F6F3]">SKORCAST</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-[#4ADE80]/15 text-[#4ADE80] border border-[#4ADE80]/30 font-medium">Wasit</span>
            </div>
            <form method="POST" action="/logout" class="m-0">
                @csrf
                <button type="submit" class="text-sm text-[#9FB0A6] hover:text-[#F2F6F3] transition-colors">Keluar</button>
            </form>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-5 sm:px-8 pt-10 sm:pt-14 pb-10 sm:pb-12">
        <p class="text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase text-[#4ADE80] mb-4">Panel Wasit</p>
        <h1 class="font-display leading-[1.05] tracking-[-0.02em] text-3xl sm:text-5xl">
            Pilih turnamen<br>
            <span class="text-[#4ADE80]">untuk diwasiti.</span>
        </h1>
        <p class="mt-4 text-[#9FB0A6] text-base sm:text-lg leading-relaxed max-w-[46ch]">
            Masuk ke bracket, lalu ketuk tombol skor di tiap pertandingan yang sedang berjalan.
        </p>
    </section>

    <section class="max-w-6xl mx-auto px-5 sm:px-8 pb-14 sm:pb-20">
        <div class="max-w-3xl">

            {{-- Berjalan --}}
            <section class="mb-12">
                <h2 class="flex items-center gap-2 mb-4 font-display text-lg sm:text-xl tracking-[-0.02em] text-amber-300">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                    Berjalan
                    <span class="text-sm font-normal text-[#9FB0A6]">({{ $ongoing->count() }})</span>
                </h2>
                @forelse($ongoing as $t)
                    <div class="mb-3 p-4 bg-[#101412] border border-white/8 rounded-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[#F2F6F3] truncate">{{ $t->name }}</p>
                                <p class="text-sm text-[#9FB0A6]">{{ $t->participants_count }} peserta · {{ $t->teams_count }} tim</p>
                            </div>
                            <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 whitespace-nowrap">Berjalan</span>
                            <a href="/w/{{ $t->code }}"
                               class="flex-none inline-flex items-center justify-center h-11 px-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] text-sm font-bold rounded-xl transition-all no-underline">
                                Masuk Bracket
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#9FB0A6]">Belum ada turnamen berjalan.</p>
                @endforelse
            </section>

            {{-- Belum mulai --}}
            <section class="mb-12">
                <h2 class="flex items-center gap-2 mb-4 font-display text-lg sm:text-xl tracking-[-0.02em] text-[#F2F6F3]">
                    <span class="w-2 h-2 rounded-full bg-white/30 inline-block"></span>
                    Belum mulai
                    <span class="text-sm font-normal text-[#9FB0A6]">({{ $draft->count() }})</span>
                </h2>
                @forelse($draft as $t)
                    <div class="mb-3 p-4 bg-[#101412] border border-white/8 rounded-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[#F2F6F3] truncate">{{ $t->name }}</p>
                                <p class="text-sm text-[#9FB0A6]">
                                    {{ $t->participants_count }} peserta
                                    @if($t->use_groups) · {{ $t->group_count }} kelompok @endif
                                </p>
                            </div>
                            <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-white/5 text-[#9FB0A6] border border-white/15 whitespace-nowrap">Belum mulai</span>
                            <a href="/w/{{ $t->code }}"
                               class="flex-none inline-flex items-center justify-center h-11 px-4 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] text-sm font-semibold rounded-xl transition-all no-underline">
                                Lihat Bracket
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#9FB0A6]">Belum ada turnamen baru.</p>
                @endforelse
            </section>

            {{-- Selesai --}}
            <section class="mb-10">
                <h2 class="flex items-center gap-2 mb-4 font-display text-lg sm:text-xl tracking-[-0.02em] text-[#4ADE80]">
                    <span class="w-2 h-2 rounded-full bg-[#4ADE80] inline-block"></span>
                    Selesai
                    <span class="text-sm font-normal text-[#9FB0A6]">({{ $completed->count() }})</span>
                </h2>
                @forelse($completed as $t)
                    <div class="mb-3 p-4 bg-[#101412] border border-white/8 rounded-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[#F2F6F3] truncate">{{ $t->name }}</p>
                                <p class="text-sm text-[#9FB0A6]">{{ $t->participants_count }} peserta · {{ $t->teams_count }} tim</p>
                            </div>
                            <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-[#4ADE80]/10 text-[#4ADE80] border border-[#4ADE80]/30 whitespace-nowrap">Selesai</span>
                            <a href="/w/{{ $t->code }}"
                               class="flex-none inline-flex items-center justify-center h-11 px-4 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] text-sm font-semibold rounded-xl transition-all no-underline">
                                Lihat Hasil
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#9FB0A6]">Belum ada turnamen selesai.</p>
                @endforelse
            </section>

        </div>
    </section>
</div>
