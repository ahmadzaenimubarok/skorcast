{{--
THESIS: Halaman daftar turnamen bergaya landing — pengunjung melihat apa yang sedang jalan & ke mana harus bertindak, dalam dunia visual Skor Cast yang utuh (header, hero, daftar, CTA, footer).
OWN-WORLD: Panggung hitam arena malam; hijau court #4ADE80 satu-satunya aksen; amber untuk yang LIVE; kartu gelap ber-batas tipis.
STORY: Hero menyambut, daftar menunjukkan 3 keadaan turnamen (berjalan/belum mulai/selesai), tiap baris satu keputusan; CTA penutup mengajak bikin turnamen sendiri.
FIRST VIEWPORT: Header SKORCAST + nav; hero ringkas "Turnamen komunitas, dalam satu layar."; di bawahnya langsung daftar Berjalan.
FORM: Hero → daftar per status → CTA penutup; footer.
FINISH: verified by feature test + curl internal/HTTPS; tampil utuh seperti landing.
--}}

<div>
{{-- HERO (ringkas, bukan jualan penuh) --}}
<section class="max-w-6xl mx-auto px-5 sm:px-8 pt-10 sm:pt-14 pb-10 sm:pb-12">
    <p class="text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase text-[#4ADE80] mb-4">Daftar turnamen</p>
    <h1 class="font-display leading-[1.05] tracking-[-0.02em] text-3xl sm:text-5xl">
        Turnamen komunitas,<br>
        <span class="text-[#4ADE80]">dalam satu layar.</span>
    </h1>
    <p class="mt-4 text-[#9FB0A6] text-base sm:text-lg leading-relaxed max-w-[46ch]">
        Cari turnamen untuk diikuti, pantau yang sedang berjalan, atau lihat hasil yang sudah tuntas.
    </p>
</section>

{{-- DAFTAR TURNAMEN --}}
<section class="max-w-6xl mx-auto px-5 sm:px-8 pb-14 sm:pb-20">
    <div class="max-w-3xl">

        {{-- Berjalan (didahulukan — yang hidup paling relevan) --}}
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
                        <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 whitespace-nowrap">
                            Berjalan
                        </span>
                        <a href="/t/{{ $t->code }}"
                           class="flex-none inline-flex items-center justify-center h-11 px-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] text-sm font-bold rounded-xl transition-all no-underline">
                            Lihat Bracket
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[#9FB0A6]">Belum ada turnamen berjalan.</p>
            @endforelse
        </section>

        {{-- Belum mulai (dulu: Draft — user: kata 'draft' kurang umum) --}}
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
                                @if($t->use_groups)
                                    · {{ $t->group_count }} kelompok
                                @endif
                            </p>
                        </div>
                        <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-white/5 text-[#9FB0A6] border border-white/15 whitespace-nowrap">
                            Belum mulai
                        </span>
                        <a href="/r/{{ $t->code }}"
                           class="flex-none inline-flex items-center justify-center h-11 px-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] text-sm font-bold rounded-xl transition-all no-underline">
                            Daftar
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[#9FB0A6]">
                    Belum ada turnamen baru.
                    <a href="/admin" class="text-[#4ADE80] hover:text-[#5FE98F] underline underline-offset-2">Buat yang pertama</a>.
                </p>
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
                        <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-[#4ADE80]/10 text-[#4ADE80] border border-[#4ADE80]/30 whitespace-nowrap">
                            Selesai
                        </span>
                        <a href="/t/{{ $t->code }}"
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

{{-- CTA PENUTUP — konsisten dengan landing --}}
<section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5 text-center">
    <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Punya turnamen sendiri?</h2>
    <p class="mt-3 text-[#9FB0A6]">Buat, bagikan link, dan biarkan peserta daftar sendiri dari HP.</p>
    <a href="/admin"
       class="mt-7 inline-flex items-center justify-center gap-2 px-8 py-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] font-bold text-base rounded-xl transition-all no-underline">
        Buat Turnamen
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
        </svg>
    </a>
</section>
</div>
