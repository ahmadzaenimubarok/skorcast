{{--
THESIS: Daftar turnamen publik — pengunjung melihat apa yang sedang jalan & ke mana harus bertindak, tanpa login.
OWN-WORLD: Panggung hitam arena malam Skor Cast; hijau court #4ADE80 sebagai satu-satunya aksen; amber untuk yang sedang LIVE; garis tipis pemisah antar grup.
STORY: Dalam satu layar terlihat 3 keadaan turnamen (belum mulai/berjalan/selesai); tiap baris satu keputusan: daftar, lihat bracket, lihat hasil.
FIRST VIEWPORT: Judul "Turnamen" + jumlah total; kelompok Berjalan paling atas, lalu Belum mulai, lalu Selesai — yang hidup didahulukan.
FORM: Daftar status sebagai bagian; tiap bagian = heading + deretan baris; aksi di kanan tiap baris.
FINISH: verified by feature test + curl internal/HTTPS; hasil = halaman yang benar-benar menampilkan data live.
--}}
<div class="max-w-3xl mx-auto">
    {{-- Heading --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-[-0.02em]">Turnamen</h1>
        <span class="text-sm text-gray-400">{{ $draft->count() + $ongoing->count() + $completed->count() }} total</span>
    </div>

    {{-- Berjalan (didahulukan — yang hidup paling relevan) --}}
    <section class="mb-12">
        <h2 class="flex items-center gap-2 mb-4 font-semibold text-amber-300">
            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
            Berjalan
            <span class="text-sm font-normal text-gray-500">({{ $ongoing->count() }})</span>
        </h2>
        @forelse($ongoing as $t)
            <div class="mb-3 p-4 bg-gray-900 border border-gray-800 rounded-xl">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-100 truncate">{{ $t->name }}</p>
                        <p class="text-sm text-gray-400">{{ $t->participants_count }} peserta · {{ $t->teams_count }} tim</p>
                    </div>
                    <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 whitespace-nowrap">
                        Berjalan
                    </span>
                    <a href="/t/{{ $t->code }}"
                       class="flex-none inline-flex items-center justify-center h-11 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors no-underline">
                        Lihat Bracket
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Belum ada turnamen berjalan.</p>
        @endforelse
    </section>

    {{-- Belum mulai (dulu: Draft — user: kata 'draft' kurang umum) --}}
    <section class="mb-12">
        <h2 class="flex items-center gap-2 mb-4 font-semibold text-gray-200">
            <span class="w-2 h-2 rounded-full bg-gray-500 inline-block"></span>
            Belum mulai
            <span class="text-sm font-normal text-gray-500">({{ $draft->count() }})</span>
        </h2>
        @forelse($draft as $t)
            <div class="mb-3 p-4 bg-gray-900 border border-gray-800 rounded-xl">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-100 truncate">{{ $t->name }}</p>
                        <p class="text-sm text-gray-400">
                            {{ $t->participants_count }} peserta
                            @if($t->use_groups)
                                · {{ $t->group_count }} kelompok
                            @endif
                        </p>
                    </div>
                    <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-gray-700/40 text-gray-300 border border-gray-600 whitespace-nowrap">
                        Belum mulai
                    </span>
                    <a href="/r/{{ $t->code }}"
                       class="flex-none inline-flex items-center justify-center h-11 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors no-underline">
                        Daftar
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">
                Belum ada turnamen baru.
                <a href="/admin" class="text-emerald-400 hover:text-emerald-300 underline underline-offset-2">Buat yang pertama</a>.
            </p>
        @endforelse
    </section>

    {{-- Selesai --}}
    <section class="mb-10">
        <h2 class="flex items-center gap-2 mb-4 font-semibold text-emerald-300">
            <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
            Selesai
            <span class="text-sm font-normal text-gray-500">({{ $completed->count() }})</span>
        </h2>
        @forelse($completed as $t)
            <div class="mb-3 p-4 bg-gray-900 border border-gray-800 rounded-xl">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-100 truncate">{{ $t->name }}</p>
                        <p class="text-sm text-gray-400">{{ $t->participants_count }} peserta · {{ $t->teams_count }} tim</p>
                    </div>
                    <span class="flex-none self-start sm:self-auto text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 whitespace-nowrap">
                        Selesai
                    </span>
                    <a href="/t/{{ $t->code }}"
                       class="flex-none inline-flex items-center justify-center h-11 px-4 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-700 text-sm font-medium rounded-lg transition-colors no-underline">
                        Lihat Hasil
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Belum ada turnamen selesai.</p>
        @endforelse
    </section>
</div>
