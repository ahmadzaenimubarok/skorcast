@section('title', '🏸 ' . $tournament->name . ' — Live Bracket | Skor Cast')
@section('meta_description', 'Siapa juaranya? Pantau bracket live ' . $tournament->name . ' — Skor Cast 🏸')

@push('meta')
    <meta property="og:type" content="website">
    <meta property="og:title" content="🏸 {{ $tournament->name }} — Live Bracket">
    <meta property="og:description" content="Siapa juaranya? Pantau bracket live {{ $tournament->name }} — Skor Cast 🏸">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Skor Cast">
    <meta property="og:image" content="{{ url('/og-image.png') }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

<div>
    {{-- Header --}}
    <div class="text-center mb-8 mt-4">
        <h1 class="text-4xl font-bold tracking-tight break-words">🏸 {{ $tournament->name }}</h1>
        <div class="mt-2 flex items-center justify-center gap-3">
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                @if($tournament->status === 'draft') bg-gray-700 text-gray-400
                @elseif($tournament->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                @endif
            ">
                {{ $tournament->status === 'ongoing' ? 'LIVE' : ($tournament->status === 'completed' ? 'SELESAI' : 'Akan Datang') }}
            </span>
            @if($tournament->status === 'ongoing')
                <span class="flex items-center gap-1.5 text-xs text-red-400">
                    <span class="inline-block w-2 h-2 bg-red-500 rounded-full polling-indicator"></span>
                    Live
                </span>
            @endif
            <button type="button" onclick="shareBracket()" id="shareBtn"
                    class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Bagikan
            </button>
        </div>
    </div>

    <script>
        function shareBracket() {
            const url = window.location.href;
            const title = @json($tournament->name);
            const text = 'Ayo dukung tim favoritmu! Live bracket & skor di sini 🏸';
            const btn = document.getElementById('shareBtn');
            const reset = () => { btn.innerHTML = btn.dataset.label; };
            if (!btn.dataset.label) btn.dataset.label = btn.innerHTML;
            if (navigator.share) {
                navigator.share({ title, text, url }).catch(() => {});
            } else {
                navigator.clipboard.writeText(text + ' ' + url).then(() => {
                    btn.innerHTML = '<span class="text-emerald-400">✓ Tersalin!</span>';
                    setTimeout(reset, 2000);
                }).catch(() => {
                    btn.innerHTML = '<span class="text-red-400">Gagal salin</span>';
                    setTimeout(reset, 2000);
                });
            }
        }
    </script>

    {{-- Champion Banner --}}
    @if($champion)
        <div class="text-center mb-8 p-6 bg-gradient-to-r from-amber-900/30 via-emerald-900/30 to-amber-900/30 border border-amber-700/50 rounded-2xl">
            <p class="text-sm text-amber-400 uppercase tracking-widest mb-1">Juara</p>
            <h2 class="text-3xl font-bold text-emerald-300 break-words">🏆 {{ $champion->name }}</h2>
        </div>
    @endif

    {{-- Bracket --}}
    @if($tournament->gameMatches->count() > 0 && count($bracketLayout['rounds']) > 0)
        <div class="overflow-x-auto pb-8">
            <div class="relative mx-auto"
                 style="width: {{ $bracketLayout['width'] }}px; height: {{ $bracketLayout['height'] }}px;">

                {{-- Round headers --}}
                @foreach ($bracketLayout['rounds'] as $round => $matches)
                    <div class="absolute top-0 flex items-center justify-center"
                         style="left: {{ $bracketLayout['roundLeft'][$round] }}px; width: 256px; height: {{ $bracketLayout['headerH'] }}px;">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider text-center">
                            {{ $bracketLayout['roundNames'][$round] ?? ('Ronde ' . $round) }}
                            @if($loop->last)<span class="text-emerald-500 ml-1">🏆</span>@endif
                        </h3>
                    </div>
                @endforeach

                {{-- Match cards --}}
                @foreach ($bracketLayout['rounds'] as $round => $matches)
                    <div class="absolute top-0" style="left: {{ $bracketLayout['roundLeft'][$round] }}px;">
                        @foreach ($matches as $match)
                            <div @if($match->status === 'completed' && $match->winner) wire:click="openMatchDetail({{ $match->id }})" @endif
                                 class="absolute w-64 flex flex-col bg-gray-900 border rounded-xl overflow-hidden
                                        {{ $match->status === 'ongoing' ? 'border-amber-600/70' : ($match->status === 'completed' ? 'border-emerald-800/70' : 'border-gray-800') }}
                                        {{ $match->status === 'completed' && $match->winner ? 'cursor-pointer hover:border-emerald-600/70 hover:bg-gray-800/40 transition-colors' : '' }} shadow-[0_1px_0_0_rgba(255,255,255,0.04)]"
                                 style="top: {{ $bracketLayout['tops'][$match->id] }}px; height: {{ $bracketLayout['cardH'] }}px;">

                                {{-- Team 1: nama+peserta kiri, kotak skor kanan --}}
                                <div class="flex items-start gap-2 px-3 pt-2 {{ $match->isTeam1Winner() ? 'text-emerald-300' : ($match->team1 ? ($match->status === 'completed' ? 'text-gray-500' : 'text-gray-200') : 'text-gray-600') }}">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold truncate">
                                            @if($match->team1)
                                                {{ $match->team1->name }}
                                            @elseif($match->isBye())
                                                <span class="text-yellow-600">BYE</span>
                                            @else
                                                —
                                            @endif
                                        </div>
                                        @if($match->team1 && $match->team1->members->isNotEmpty())
                                            <div class="text-[11px] font-normal text-gray-500 truncate">{{ $match->team1->membersList() }}</div>
                                        @endif
                                    </div>
                                    @if(!$match->isBye())
                                        @php $totalGames = $tournament->games_to_win === 1 ? 1 : ($tournament->games_to_win * 2 - 1); @endphp
                                        <div class="flex-none flex items-center gap-1">
                                            @for($gi = 0; $gi < $totalGames; $gi++)
                                                @php
                                                    $g = ($match->games_detail[$gi] ?? ['t1' => 0, 't2' => 0]);
                                                    $gt1 = $g['t1'] ?? 0; $gt2 = $g['t2'] ?? 0;
                                                    $gw = $match->gameWinner($gt1, $gt2);
                                                @endphp
                                                <span class="inline-flex flex-col items-center justify-center rounded-md min-w-[2.25rem] py-0.5
                                                             {{ $gw === 1 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-gray-800 text-gray-400' }}">
                                                    <span class="text-[9px] leading-none text-gray-500 mb-0.5">G{{ $gi + 1 }}</span>
                                                    <span class="text-xs font-mono tabular-nums font-semibold">{{ $gt1 }}</span>
                                                </span>
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                <div class="h-px bg-gray-800"></div>

                                {{-- Team 2 --}}
                                <div class="flex items-start gap-2 px-3 pt-2 {{ $match->isTeam2Winner() ? 'text-emerald-300' : ($match->team2 ? ($match->status === 'completed' ? 'text-gray-500' : 'text-gray-200') : 'text-gray-600') }}">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold truncate">
                                            @if($match->team2)
                                                {{ $match->team2->name }}
                                            @elseif($match->isBye())
                                                <span class="text-yellow-600">BYE</span>
                                            @else
                                                —
                                            @endif
                                        </div>
                                        @if($match->team2 && $match->team2->members->isNotEmpty())
                                            <div class="text-[11px] font-normal text-gray-500 truncate">{{ $match->team2->membersList() }}</div>
                                        @endif
                                    </div>
                                    @if(!$match->isBye())
                                        @php $totalGames = $tournament->games_to_win === 1 ? 1 : ($tournament->games_to_win * 2 - 1); @endphp
                                        <div class="flex-none flex items-center gap-1">
                                            @for($gi = 0; $gi < $totalGames; $gi++)
                                                @php
                                                    $g = ($match->games_detail[$gi] ?? ['t1' => 0, 't2' => 0]);
                                                    $gt1 = $g['t1'] ?? 0; $gt2 = $g['t2'] ?? 0;
                                                    $gw = $match->gameWinner($gt1, $gt2);
                                                @endphp
                                                <span class="inline-flex flex-col items-center justify-center rounded-md min-w-[2.25rem] py-0.5
                                                             {{ $gw === 2 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-gray-800 text-gray-400' }}">
                                                    <span class="text-[9px] leading-none text-gray-500 mb-0.5">G{{ $gi + 1 }}</span>
                                                    <span class="text-xs font-mono tabular-nums font-semibold">{{ $gt2 }}</span>
                                                </span>
                                            @endfor
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer --}}
                                @if($match->status === 'ongoing')
                                    <a href="{{ route('public.scoreboard', ['code' => $tournament->code, 'gameMatch' => $match->id]) }}"
                                       class="mt-auto block w-full h-9 flex items-center justify-center gap-1.5 text-xs font-semibold bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border-t border-emerald-700/40 transition-colors">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 polling-indicator"></span>
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                        Scoreboard
                                    </a>
                                @elseif($match->status === 'completed' && $match->winner)
                                    <div class="mt-auto h-9 flex items-center justify-center gap-1.5 text-xs font-medium border-t border-gray-800 {{ $match->isBye() ? 'text-yellow-500' : 'text-emerald-400' }}">
                                        @if($match->isBye())
                                            ↪ {{ $match->winner->name }} (BYE)
                                        @else
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            {{ $match->winner->name }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- Connectors --}}
                <svg class="absolute top-0 left-0 pointer-events-none"
                     width="{{ $bracketLayout['width'] }}" height="{{ $bracketLayout['height'] }}"
                     viewBox="0 0 {{ $bracketLayout['width'] }} {{ $bracketLayout['height'] }}">
                    @foreach ($bracketLayout['lines'] as $line)
                        <line x1="{{ $line[0] }}" y1="{{ $line[1] }}"
                              x2="{{ $line[2] }}" y2="{{ $line[3] }}"
                              stroke="{{ $line[4] ?? false ? '#10b981' : '#52525b' }}"
                              stroke-width="2" stroke-linecap="round"/>
                    @endforeach
                </svg>

            </div>
        </div>
    @else
        <div class="text-center py-20 text-gray-600">
            <p class="text-6xl mb-4">🏸</p>
            <p>Bracket belum tersedia.</p>
        </div>
    @endif

    <div class="text-center text-xs text-gray-700 mt-8 pb-8">
        <a href="https://github.com/ahmadzaenimubarok/skorcast" target="_blank" class="hover:text-gray-500 transition-colors">🐙 GitHub</a>
        &middot; Skor Cast &middot; skorcast.online
    </div>

    @include('livewire.partials.match-detail-modal')

    {{-- Livewire polling — live selama ada match yg berjalan --}}
    @if($tournament->status !== 'completed' && $tournament->gameMatches->contains(fn($m) => $m->status === 'ongoing'))
        <div wire:poll.3000ms="$refresh"></div>
    @endif
</div>
