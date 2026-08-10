<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Skor Cast — Aplikasi Turnamen & Skor Badminton Online</title>
    <meta name="description" content="Aplikasi skor badminton untuk turnamen komunitas — peserta daftar lewat link, skor masuk dari HP, bracket tersusun dan maju otomatis. Tanpa aplikasi, tanpa akun.">
    <link rel="canonical" href="https://skorcast.online/">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#0B0E0C">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Skor Cast">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="Skor Cast — Aplikasi Turnamen & Skor Badminton Online">
    <meta property="og:description" content="Aplikasi skor badminton untuk turnamen komunitas — daftar via link, skor dari HP, bracket otomatis. Tanpa aplikasi, tanpa akun.">
    <meta property="og:url" content="https://skorcast.online/">
    <meta property="og:image" content="https://skorcast.online/og-image.png">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Skor Cast — Aplikasi Turnamen & Skor Badminton Online">
    <meta name="twitter:description" content="Aplikasi skor badminton untuk turnamen komunitas — daftar via link, skor dari HP, bracket otomatis. Tanpa aplikasi, tanpa akun.">
    <meta name="twitter:image" content="https://skorcast.online/og-image.png">

    {{-- Structured data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebApplication",
        "name": "Skor Cast",
        "url": "https://skorcast.online/",
        "applicationCategory": "SportsApplication",
        "operatingSystem": "Any",
        "description": "Aplikasi skor badminton untuk turnamen komunitas — peserta daftar lewat link, skor masuk dari HP, bracket tersusun dan maju otomatis. Tanpa aplikasi, tanpa akun.",
        "inLanguage": "id",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "IDR"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            {
                "@@type": "Question",
                "name": "Bagaimana cara buat bracket turnamen badminton?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Buat turnamen di Skor Cast, bagikan link pendaftaran, lalu peserta daftar sendiri dari HP. Bracket single elimination tersusun otomatis — pemenang tiap pertandingan maju sendiri ke putaran berikutnya, tanpa panitia merangkai."
                }
            },
            {
                "@@type": "Question",
                "name": "Berapa skor badminton sampai menang?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Sesuai aturan resmi BWF: rally point 21, menang dengan selisih 2 angka, maksimal 30. Skor Cast menghitungnya otomatis di setiap pertandingan."
                }
            },
            {
                "@@type": "Question",
                "name": "Apakah peserta harus install aplikasi?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Tidak. Peserta cukup buka link dari HP — daftar, lihat bracket, dan ikuti skor langsung dari browser. Tanpa akun, tanpa install."
                }
            },
            {
                "@@type": "Question",
                "name": "Untuk siapa aplikasi skor badminton ini?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Untuk penyelenggara turnamen komunitas: RT, kampus, gereja, perusahaan, dan klub badminton. Dari beberapa peserta sampai puluhan, bracket menyesuaikan otomatis."
                }
            },
            {
                "@@type": "Question",
                "name": "Apakah Skor Cast gratis?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Gratis untuk dipakai. Tidak ada biaya langganan — buat turnamen dan bagikan link-nya sekarang."
                }
            }
        ]
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-display { font-family: 'Archivo Black', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
        .rise { animation: rise .65s cubic-bezier(.22,.9,.32,1) both; }
        .rise-1 { animation-delay: .05s; }
        .rise-2 { animation-delay: .15s; }
        .rise-3 { animation-delay: .25s; }
        @keyframes livepulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
        .live-dot { animation: livepulse 1.5s infinite; }
    </style>
</head>
<body class="font-sans antialiased bg-[#0B0E0C] text-[#F2F6F3] min-h-dvh overflow-x-hidden">
<!--
THESIS: Landing ini membuktikan mekanisme produk — turnamen komunitas yang jalan otomatis — dengan memamerkan bracket & skor live; menolak hero SaaS default (gradient, icon cards, klaim abstrak).
OWN-WORLD: Panggung hitam arena malam; hijau court #4ADE80; garis lapangan putih tipis; angka tabular raksasa; badge LIVE amber. Body Instrument Sans, display Archivo Black.
STORY: Pengunjung paham dalam 3 detik bahwa turnamen bisa dibuat tanpa kertas dan skor jalan otomatis; tindakan: buat turnamen.
FIRST VIEWPORT: Wordmark kiri atas; H1 besar kiri dengan "skor jalan otomatis" hijau; dua CTA (Buat Turnamen primer); mockup scoreboard nyata (panel hijau, kartu gelap, 21-17, LIVE) di kanan sebagai bukti.
FORM: bracket demo naik setelah hero, row-step ber-nomor (alur turnamen), grid fitur text-led, CTA penutup.
FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, and DESIGN.md
-->

    {{-- NAV --}}
    <header class="max-w-6xl mx-auto px-5 sm:px-8 pt-6 flex items-center justify-between">
        <a href="/" class="font-display text-lg tracking-[0.12em] text-[#F2F6F3] no-underline">SKOR<span class="text-[#4ADE80]">CAST</span></a>
        <nav class="flex items-center gap-1 sm:gap-2">
            <a href="/s" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Scoreboard</a>
            <a href="/turnamen" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Turnamen</a>
        </nav>
    </header>

    {{-- HERO --}}
    <section class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-14 sm:pt-20 pb-16 sm:pb-24">
        {{-- Garis lapangan dekoratif --}}
        <svg class="absolute -z-10 inset-0 w-full h-full opacity-[0.06]" viewBox="0 0 800 500" fill="none" stroke="white" stroke-width="1" aria-hidden="true">
            <rect x="40" y="40" width="720" height="420"/>
            <line x1="40" y1="250" x2="760" y2="250"/>
            <circle cx="400" cy="250" r="70"/>
            <line x1="400" y1="40" x2="400" y2="250"/>
        </svg>

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-10 items-center">
            <div class="rise rise-1">
                <p class="text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase text-[#4ADE80] mb-5">Aplikasi skor badminton · bracket otomatis</p>
                <h1 class="font-display leading-[1.02] tracking-[-0.02em] text-[2.6rem] sm:text-6xl xl:text-7xl">
                    Turnamen tanpa kertas,<br>
                    <span class="text-[#4ADE80]">skor jalan otomatis.</span>
                </h1>
                <p class="mt-6 text-[#9FB0A6] text-base sm:text-lg leading-relaxed max-w-[46ch]">
                    Skor Cast mengatur turnamen badminton komunitasmu — peserta daftar lewat link, skor masuk dari HP, bracket tersusun dan maju otomatis. Tanpa aplikasi, tanpa akun.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="/admin"
                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] font-bold text-base rounded-xl transition-all no-underline">
                        Buat Turnamen
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="/s"
                       class="inline-flex items-center justify-center px-8 py-4 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] font-semibold text-base rounded-xl transition-all no-underline">
                        Coba Scoreboard
                    </a>
                </div>
            </div>

            {{-- Mockup scoreboard nyata --}}
            <div class="rise rise-2 relative">
                <div class="rounded-2xl bg-[#4ADE80] p-2.5 sm:p-3 shadow-[0_30px_90px_-25px_rgba(74,222,128,0.4)]">
                    <div class="bg-[#1e1e2e] rounded-xl px-4 py-4 sm:px-7 sm:py-6">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold tracking-wide text-amber-300 bg-amber-500/15 border border-amber-500/30 rounded-full px-2.5 py-1">GAME 2</span>
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 live-dot"></span>LIVE
                            </span>
                        </div>
                        <div class="flex items-stretch mt-4">
                            <div class="flex-1 text-center">
                                <div class="font-display text-white text-6xl sm:text-7xl leading-none tabular-nums">21</div>
                                <div class="text-xs sm:text-sm font-bold text-[#22c55e] mt-2">TIM 1</div>
                            </div>
                            <div class="w-px bg-white/15 mx-3 sm:mx-5"></div>
                            <div class="flex-1 text-center">
                                <div class="font-display text-white text-6xl sm:text-7xl leading-none tabular-nums">17</div>
                                <div class="text-xs sm:text-sm font-bold text-[#22c55e] mt-2">TIM 2</div>
                            </div>
                        </div>
                        <div class="flex justify-center gap-1.5 mt-5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="w-2 h-2 rounded-full border border-white/40"></span>
                        </div>
                        <div class="text-center text-[9px] text-gray-500 mt-3 hidden sm:block">+1 ketuk · tahan untuk -1</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TURNAMEN (bracket demo) --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Dari daftar ke champion.</h2>
                <p class="mt-5 text-[#9FB0A6] leading-relaxed max-w-[46ch]">Peserta daftar lewat link yang kamu bagikan. Bracket tersusun otomatis — hasil pertandingan langsung mengisi dan memajukan putaran.</p>
                <a href="/admin" class="mt-7 inline-flex items-center gap-2 px-7 py-3.5 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] font-semibold text-sm rounded-xl transition-all no-underline">
                    Buat Turnamen
                </a>
            </div>

            {{-- Bracket demo (satu SVG proporsional — kartu + garis sinkron) --}}
            <div class="relative bg-[#101412] border border-white/8 rounded-2xl px-4 sm:px-6 py-4 sm:py-5 overflow-hidden">
                <svg class="block w-full h-auto" viewBox="0 0 640 240" fill="none" aria-hidden="true">
                    {{-- GARIS PENGHUBUNG --}}
                    <g stroke="rgba(255,255,255,0.18)" stroke-width="1.5">
                        <path d="M140 38 H160 M140 96 H160 M160 38 V96 M160 67 H300"/>
                        <path d="M140 154 H160 M140 212 H160 M160 154 V212 M160 183 H300"/>
                        <path d="M440 67 H460 M440 183 H460 M460 67 V183 M460 125 H470"/>
                    </g>

                    {{-- PUTARAN 1 (4 tim) --}}
                    <g>
                        <rect x="0" y="16" width="140" height="44" rx="8" fill="#151A17" stroke="rgba(255,255,255,0.10)"/>
                        <text x="12" y="43" fill="#F2F6F3" font-size="13">Tim Alpha</text>
                        <rect x="0" y="74" width="140" height="44" rx="8" fill="#151A17" stroke="rgba(255,255,255,0.10)"/>
                        <text x="12" y="101" fill="#F2F6F3" font-size="13">Tim Beta</text>
                        <rect x="0" y="132" width="140" height="44" rx="8" fill="#151A17" stroke="rgba(255,255,255,0.10)"/>
                        <text x="12" y="159" fill="#F2F6F3" font-size="13">Tim Gamma</text>
                        <rect x="0" y="190" width="140" height="44" rx="8" fill="#151A17" stroke="rgba(255,255,255,0.10)"/>
                        <text x="12" y="217" fill="#F2F6F3" font-size="13">Tim Delta</text>
                    </g>

                    {{-- SEMIFINAL (2 pemenang) --}}
                    <g>
                        <rect x="300" y="45" width="140" height="44" rx="8" fill="#1A211D" stroke="rgba(255,255,255,0.15)"/>
                        <text x="312" y="72" fill="#F2F6F3" font-size="13">Pemenang Semi A</text>
                        <rect x="300" y="161" width="140" height="44" rx="8" fill="#1A211D" stroke="rgba(255,255,255,0.15)"/>
                        <text x="312" y="188" fill="#F2F6F3" font-size="13">Pemenang Semi B</text>
                    </g>

                    {{-- FINAL (champion) --}}
                    <g>
                        <rect x="470" y="103" width="130" height="44" rx="8" fill="rgba(74,222,128,0.10)" stroke="rgba(74,222,128,0.50)"/>
                        <text x="482" y="130" fill="#4ADE80" font-size="13" font-weight="700">Champion</text>
                    </g>
                </svg>
                <span class="absolute bottom-2.5 right-4 text-[9px] tracking-wider text-white/25 uppercase">contoh bracket</span>
            </div>
        </div>
    </section>

    {{-- CARA PAKAI --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Tiga langkah, langsung main.</h2>
        <div class="mt-10">
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-10 py-6 border-t border-white/8">
                <div class="font-display text-4xl text-white/15 shrink-0 w-16">1</div>
                <div class="flex-1">
                    <h3 class="text-lg sm:text-xl font-bold">Buat & bagikan link</h3>
                    <p class="mt-1.5 text-[#9FB0A6] leading-relaxed">Bikin turnamen sekali klik, dapat link pendaftaran. Bagikan ke grup — semuanya daftar dari HP masing-masing.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-10 py-6 border-t border-white/8">
                <div class="font-display text-4xl text-white/15 shrink-0 w-16">2</div>
                <div class="flex-1">
                    <h3 class="text-lg sm:text-xl font-bold">Peserta daftar sendiri</h3>
                    <p class="mt-1.5 text-[#9FB0A6] leading-relaxed">Nama masuk, bracket langsung tersusun otomatis. Tidak ada kertas, tidak ada panitia yang merangkai.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-10 py-6 border-t border-white/8">
                <div class="font-display text-4xl text-white/15 shrink-0 w-16">3</div>
                <div class="flex-1">
                    <h3 class="text-lg sm:text-xl font-bold">Skor masuk, bracket maju</h3>
                    <p class="mt-1.5 text-[#9FB0A6] leading-relaxed">Ketuk skor dari lapangan — hasil langsung mengisi bracket, pemenang maju ke putaran berikutnya.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Kenapa Skor Cast.</h2>
        <div class="mt-10 grid sm:grid-cols-2 gap-y-10 gap-x-14">
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Bracket tersusun sendiri</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Turnamen single elimination: peserta daftar lewat link, bracket tersusun dan maju otomatis ke putaran berikutnya.</p>
            </div>
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Satu link, semua lihat</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Setiap layar yang membuka link menampilkan skor yang sama, saat itu juga. Tidak perlu sinkronisasi apa pun.</p>
            </div>
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Angka kebaca dari jauh</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Fullscreen landscape dengan angka raksasa dan kontras tinggi — terlihat dari seberang lapangan.</p>
            </div>
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Tanpa akun, tanpa install</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Tidak ada signup, tidak ada aplikasi. Buka, main, selesai.</p>
            </div>
        </div>
    </section>

    {{-- FAQ — long-tail keyword & featured snippet --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Tanya jawab turnamen badminton.</h2>
        <div class="mt-8 max-w-3xl space-y-3">
            <details class="group border border-white/8 rounded-xl bg-[#101412] px-5 py-4" open>
                <summary class="cursor-pointer font-semibold text-base sm:text-lg list-none flex items-center justify-between gap-4 [&::-webkit-details-marker]:hidden">
                    Bagaimana cara buat bracket turnamen badminton?
                    <span class="text-[#4ADE80] text-sm group-open:hidden">+</span>
                    <span class="text-[#4ADE80] text-sm hidden group-open:inline">−</span>
                </summary>
                <p class="mt-3 text-[#9FB0A6] leading-relaxed text-sm sm:text-base">Buat turnamen di Skor Cast, bagikan link pendaftaran, lalu peserta daftar sendiri dari HP. Bracket single elimination tersusun otomatis — pemenang tiap pertandingan maju sendiri ke putaran berikutnya, tanpa panitia merangkai.</p>
            </details>
            <details class="group border border-white/8 rounded-xl bg-[#101412] px-5 py-4">
                <summary class="cursor-pointer font-semibold text-base sm:text-lg list-none flex items-center justify-between gap-4 [&::-webkit-details-marker]:hidden">
                    Berapa skor badminton sampai menang?
                    <span class="text-[#4ADE80] text-sm group-open:hidden">+</span>
                    <span class="text-[#4ADE80] text-sm hidden group-open:inline">−</span>
                </summary>
                <p class="mt-3 text-[#9FB0A6] leading-relaxed text-sm sm:text-base">Sesuai aturan resmi BWF: rally point 21, menang dengan selisih 2 angka, maksimal 30. Skor Cast menghitungnya otomatis di setiap pertandingan.</p>
            </details>
            <details class="group border border-white/8 rounded-xl bg-[#101412] px-5 py-4">
                <summary class="cursor-pointer font-semibold text-base sm:text-lg list-none flex items-center justify-between gap-4 [&::-webkit-details-marker]:hidden">
                    Apakah peserta harus install aplikasi?
                    <span class="text-[#4ADE80] text-sm group-open:hidden">+</span>
                    <span class="text-[#4ADE80] text-sm hidden group-open:inline">−</span>
                </summary>
                <p class="mt-3 text-[#9FB0A6] leading-relaxed text-sm sm:text-base">Tidak. Peserta cukup buka link dari HP — daftar, lihat bracket, dan ikuti skor langsung dari browser. Tanpa akun, tanpa install.</p>
            </details>
            <details class="group border border-white/8 rounded-xl bg-[#101412] px-5 py-4">
                <summary class="cursor-pointer font-semibold text-base sm:text-lg list-none flex items-center justify-between gap-4 [&::-webkit-details-marker]:hidden">
                    Untuk siapa aplikasi skor badminton ini?
                    <span class="text-[#4ADE80] text-sm group-open:hidden">+</span>
                    <span class="text-[#4ADE80] text-sm hidden group-open:inline">−</span>
                </summary>
                <p class="mt-3 text-[#9FB0A6] leading-relaxed text-sm sm:text-base">Untuk penyelenggara turnamen komunitas: RT, kampus, gereja, perusahaan, dan klub badminton. Dari beberapa peserta sampai puluhan, bracket menyesuaikan otomatis.</p>
            </details>
            <details class="group border border-white/8 rounded-xl bg-[#101412] px-5 py-4">
                <summary class="cursor-pointer font-semibold text-base sm:text-lg list-none flex items-center justify-between gap-4 [&::-webkit-details-marker]:hidden">
                    Apakah Skor Cast gratis?
                    <span class="text-[#4ADE80] text-sm group-open:hidden">+</span>
                    <span class="text-[#4ADE80] text-sm hidden group-open:inline">−</span>
                </summary>
                <p class="mt-3 text-[#9FB0A6] leading-relaxed text-sm sm:text-base">Gratis untuk dipakai. Tidak ada biaya langganan — buat turnamen dan bagikan link-nya sekarang.</p>
            </details>
        </div>
    </section>

    {{-- CLOSE CTA --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-24 border-t border-white/5 text-center">
        <h2 class="font-display text-3xl sm:text-5xl tracking-[-0.02em]">Turnamen komunitasmu,<br class="sm:hidden"> jalan sendiri.</h2>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="/admin" class="inline-flex items-center justify-center gap-2 px-10 py-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] font-bold text-base rounded-xl transition-all no-underline">
                Buat Turnamen
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="/s" class="inline-flex items-center justify-center px-10 py-4 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] font-semibold text-base rounded-xl transition-all no-underline">
                Coba Scoreboard
            </a>
        </div>
    </section>

    <footer class="max-w-6xl mx-auto px-5 sm:px-8 pb-10 pt-6 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#6B7C72]">
        <span>Skor Cast · Badminton Fun Match</span>
        <span>dibuat oleh Ahmad Zaeni Mubarok</span>
    </footer>

</body>
</html>
