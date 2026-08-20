{{-- Partial modal detail pertandingan selesai.
     Dipakai oleh WasitBracket, TournamentShow (admin), dan PublicBracket.
     Butuh variabel: $showMatchDetail (bool), $matchDetail (array|null) --}}
@if($showMatchDetail && $matchDetail)
    <div class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-black/80"
         wire:click.self="closeMatchDetail">
        <div class="w-full max-w-md bg-[#1e1e2e] border border-emerald-700/40 shadow-2xl rounded-2xl overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-white/10">
                <h3 class="text-sm font-semibold text-gray-300">Detail Pertandingan</h3>
                <button type="button" wire:click="closeMatchDetail"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="px-5 py-4 space-y-4">
                {{-- Skor akhir --}}
                <div class="flex items-center justify-center gap-4">
                    <div class="flex-1 text-right min-w-0">
                        <div class="text-sm font-semibold text-gray-200 truncate">{{ $matchDetail['team1'] }}</div>
                    </div>
                    <div class="flex items-center gap-2 font-mono tabular-nums">
                        <span class="text-2xl font-bold {{ !$matchDetail['isBye'] && $matchDetail['winner'] === $matchDetail['team1'] ? 'text-emerald-400' : 'text-gray-300' }}">{{ $matchDetail['score1'] }}</span>
                        <span class="text-gray-500">-</span>
                        <span class="text-2xl font-bold {{ !$matchDetail['isBye'] && $matchDetail['winner'] === $matchDetail['team2'] ? 'text-emerald-400' : 'text-gray-300' }}">{{ $matchDetail['score2'] }}</span>
                    </div>
                    <div class="flex-1 text-left min-w-0">
                        <div class="text-sm font-semibold text-gray-200 truncate">{{ $matchDetail['team2'] }}</div>
                    </div>
                </div>

                @if(!$matchDetail['isBye'])
                    {{-- Waktu --}}
                    <div class="flex items-center justify-center gap-6 text-center">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-500">Mulai</div>
                            <div class="text-sm font-mono text-gray-200">{{ $matchDetail['started'] ?? '—' }}</div>
                        </div>
                        <div class="text-gray-600">→</div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-500">Selesai</div>
                            <div class="text-sm font-mono text-gray-200">{{ $matchDetail['finished'] ?? '—' }}</div>
                        </div>
                        @if($matchDetail['duration'])
                            <div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-500">Durasi</div>
                                <div class="text-sm font-mono text-emerald-300">{{ $matchDetail['duration'] }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Per-game --}}
                    <div class="border-t border-white/10 pt-3">
                        <div class="text-[10px] uppercase tracking-wider text-gray-500 mb-2 text-center">Rincian Game</div>
                        <div class="space-y-1.5">
                            @foreach($matchDetail['games'] as $g)
                                <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-white/5">
                                    <span class="text-xs font-medium text-gray-400">Game {{ $g['index'] }}</span>
                                    <div class="flex items-center gap-2 font-mono tabular-nums text-sm">
                                        <span class="{{ $g['winner'] === 1 ? 'text-emerald-400 font-bold' : 'text-gray-300' }}">{{ $g['t1'] }}</span>
                                        <span class="text-gray-600">-</span>
                                        <span class="{{ $g['winner'] === 2 ? 'text-emerald-400 font-bold' : 'text-gray-300' }}">{{ $g['t2'] }}</span>
                                    </div>
                                    <span class="text-[10px] font-mono text-gray-500 w-20 text-right">
                                        {{ $g['start'] ?? '—' }} → {{ $g['end'] ?? '—' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center text-xs text-yellow-500">Pertandingan BYE — tidak ada rincian game.</div>
                @endif
            </div>
        </div>
    </div>
@endif
