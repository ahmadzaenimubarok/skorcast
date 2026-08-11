<div>
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <a href="{{ route('tournaments.index') }}" class="text-sm text-gray-500 hover:text-gray-300 mb-1 inline-block">&larr; Kembali</a>
            <h1 class="text-3xl font-bold break-words">{{ $tournament->name }}</h1>
            <div class="flex flex-wrap items-center gap-3 mt-1">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($tournament->status === 'archived') bg-gray-700/50 text-gray-500 border border-gray-600
                    @elseif($tournament->status === 'draft') bg-gray-700 text-gray-400
                    @elseif($tournament->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                    @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                    @endif
                ">
                    {{ $tournament->statusLabel() }}
                </span>
                <span class="text-sm text-gray-500 flex items-center gap-1.5" x-data="{ copied: false }">
                    Kode publik:
                    <button type="button" @click="navigator.clipboard.writeText('{{ $tournament->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-flex items-center gap-1.5 group" :title="copied ? 'Tersalin!' : 'Salin kode'">
                        <code class="text-emerald-400 bg-gray-800 px-2 py-0.5 rounded group-hover:bg-gray-700 transition-colors">{{ $tournament->code }}</code>
                        <svg x-show="!copied" class="w-4 h-4 text-gray-500 group-hover:text-gray-300 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                        <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </button>
                </span>
                @if($tournament->status === 'draft')
                    <span class="text-sm text-gray-500 ml-2">
                        Format:
                        <select wire:change="setGamesFormat($event.target.value)" class="bg-gray-800 text-gray-200 border border-gray-700 rounded px-2 py-0.5 text-xs">
                            <option value="2" {{ $tournament->games_to_win === 2 ? 'selected' : '' }}>Best of 3</option>
                            <option value="1" {{ $tournament->games_to_win === 1 ? 'selected' : '' }}>1 Game</option>
                        </select>
                    </span>
                    <span class="text-sm text-gray-500 ml-2">
                        Jenis:
                        <select wire:change="setPlayMode($event.target.value)" class="bg-gray-800 text-gray-200 border border-gray-700 rounded px-2 py-0.5 text-xs">
                            <option value="doubles" {{ $tournament->play_mode === 'doubles' ? 'selected' : '' }}>Ganda</option>
                            <option value="singles" {{ $tournament->play_mode === 'singles' ? 'selected' : '' }}>Tunggal</option>
                        </select>
                    </span>
                @endif
                <span class="text-sm text-gray-500 flex items-center gap-2 ml-2">
                    Visibilitas:
                    <button type="button" wire:click="toggleVisibility"
                            class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50
                                {{ $tournament->is_public ? 'bg-emerald-600' : 'bg-gray-600' }}"
                            role="switch" aria-checked="{{ $tournament->is_public ? 'true' : 'false' }}"
                            :title="'{{ $tournament->is_public ? 'Klik untuk privat' : 'Klik untuk publik' }}'">
                        <span class="inline-block w-4 h-4 transform rounded-full bg-white transition-transform
                            {{ $tournament->is_public ? 'translate-x-[1.4rem]' : 'translate-x-1' }}"></span>
                    </button>
                    <span class="text-xs font-medium {{ $tournament->is_public ? 'text-emerald-400' : 'text-gray-400' }}">
                        {{ $tournament->is_public ? 'Publik' : 'Privat' }}
                    </span>
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2"
             x-data="{ copied: false }">
            <button x-data="{ shared: false }" @click="
                const url = '{{ route('registration.show', $tournament->code) }}';
                if (navigator.share) {
                    navigator.share({
                        title: '{{ $tournament->name }}',
                        text: 'Daftar turnamen {{ $tournament->name }} 🏸',
                        url: url
                    }).catch(() => {});
                } else {
                    navigator.clipboard.writeText(url);
                    shared = true;
                    setTimeout(() => shared = false, 2000);
                }
            "
                    class="flex items-center gap-2 px-4 h-11 bg-emerald-900/40 hover:bg-emerald-800/60 text-emerald-300 border border-emerald-800 rounded-lg transition-colors"
                    :title="shared ? 'Tersalin!' : 'Bagikan halaman pendaftaran'">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                <span class="text-xs font-medium" x-text="shared ? 'Tersalin!' : 'Bagikan'"></span>
            </button>
            <button @click="
                navigator.clipboard.writeText('{{ route('public.bracket', $tournament->code) }}');
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
                    class="relative w-11 h-11 flex items-center justify-center bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-lg transition-colors"
                    :title="copied ? 'Tersalin!' : 'Salin tautan publik'">
                <svg x-show="!copied" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"/>
                    <circle cx="6" cy="12" r="3"/>
                    <circle cx="18" cy="19" r="3"/>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                </svg>
                <svg x-show="copied" x-cloak class="w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>
            @if($tournament->status === 'draft' && $tournament->teams->count() >= 2 && $tournament->gameMatches->count() > 0)
                <button wire:click="startTournament" class="px-4 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    Mulai Turnamen
                </button>
            @endif
            @if($tournament->status !== 'draft' && $tournament->status !== 'archived')
                <button wire:click="resetTournament" wire:confirm="Reset turnamen? Semua data pertandingan akan hilang." class="px-4 h-11 bg-red-900/50 hover:bg-red-800 text-red-300 border border-red-800 rounded-lg transition-colors text-sm">
                    Reset
                </button>
            @endif
            @if($tournament->status === 'archived')
                <button wire:click="unarchiveTournament" wire:confirm="Kembalikan turnamen?" class="px-4 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    Kembalikan
                </button>
            @else
                <button wire:click="archiveTournament" wire:confirm="Arsipkan turnamen?" class="px-4 h-11 bg-gray-700 hover:bg-gray-600 text-gray-300 border border-gray-600 rounded-lg transition-colors text-sm">
                    Arsipkan
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="mb-6 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-lg text-emerald-200 text-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 px-4 py-3 bg-red-900/50 border border-red-700 rounded-lg text-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b border-gray-700">
        <button wire:click="$set('tab', 'participants')" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $tab === 'participants' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Peserta
        </button>
        <button wire:click="$set('tab', 'teams')" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $tab === 'teams' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Tim
        </button>
        <button wire:click="$set('tab', 'bracket')" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $tab === 'bracket' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Bracket
        </button>
        <a href="{{ route('public.bracket', $tournament->code) }}" target="_blank"
           class="flex-none w-11 h-11 flex items-center justify-center text-gray-500 hover:text-gray-300 hover:bg-gray-800 rounded-lg transition-colors ml-auto"
           title="Tampilan Publik">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
        </a>
    </div>

    {{-- Tab: Participants --}}
    @if($tab === 'participants')
        <div>
            {{-- Estimasi waktu pertandingan --}}
            <div class="mb-6 rounded-xl border border-gray-700 bg-gray-800/40 p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Estimasi total permainan</p>
                        <p class="mt-1 text-2xl sm:text-3xl font-bold text-emerald-400">{{ $estimate['totalLabel'] }}</p>
                        <p class="mt-1 text-sm text-gray-400">
                            {{ $estimate['teams'] }} tim · {{ $estimate['matches'] }} pertandingan · format {{ $estimate['formatLabel'] }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-1.5 items-start sm:items-end">
                        <label class="text-xs text-gray-500">Jumlah lapangan</label>
                        <select wire:model.live="estimateCourts" class="bg-gray-800 text-gray-200 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                            @foreach([1, 2, 3, 4] as $n)
                                <option value="{{ $n }}">{{ $n }} lapangan</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-600">
                    ≈{{ $estimate['perMatch'] }} menit/pertandingan (termasuk ±{{ $estimate['break'] }} menit jeda) · bye langsung maju tanpa main
                </p>
                @if($estimate['teams'] < 2)
                    <p class="mt-2 text-xs text-amber-500/90">Tambahkan minimal 2 peserta untuk melihat estimasi.</p>
                @endif
            </div>

            @if($tournament->status === 'draft')
                {{-- Progress kuota --}}
                @if($tournament->max_participants)
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 rounded-full transition-all"
                                 style="width: {{ min(100, ($tournament->participants->count() / $tournament->max_participants) * 100) }}%"></div>
                        </div>
                        <span class="text-sm text-gray-400 flex-none">
                            {{ $tournament->participants->count() }}/{{ $tournament->max_participants }} peserta
                        </span>
                    </div>
                    @if($tournament->participants->count() >= $tournament->max_participants)
                        <div class="mb-4 px-4 py-3 bg-amber-900/40 border border-amber-700/60 rounded-lg text-amber-200 text-sm">
                            Kuota sudah penuh ({{ $tournament->max_participants }} peserta). Pendaftaran ditutup.
                        </div>
                    @endif
                @endif

                @if(!$tournament->max_participants || $tournament->participants->count() < $tournament->max_participants)
                <form wire:submit="addParticipant" class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input
                            wire:model="participantName"
                            type="text"
                            placeholder="Nama peserta..."
                            class="flex-1 h-11 px-4 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                        @if($tournament->use_groups)
                            <select wire:model="participantGroup" class="h-11 bg-gray-800 text-gray-200 border border-gray-700 rounded-lg px-3 text-sm">
                                <option value="">Kelompok...</option>
                                @foreach($tournament->groupOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @endif
                        <button type="submit" class="px-5 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                            + Tambah
                        </button>
                    </div>
                    @error('participantName') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    @if($tournament->use_groups && $tournament->groupCapacity())
                        <p class="mt-2 text-xs text-gray-500">Kuota per kelompok: maksimal {{ $tournament->groupCapacity() }} peserta ({{ $tournament->group_count }} kelompok × rata).</p>
                    @endif
                </form>
                @endif
            @endif

            <div class="space-y-2">
                @php
                    $sortedParticipants = $tournament->participants
                        ->sortBy(fn ($p) => strtolower(($p->group_name ?? '') . '|' . $p->name));
                @endphp
                @forelse ($sortedParticipants as $p)
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-800/50 rounded-lg">
                        <span class="flex items-center gap-2 min-w-0">
                            <span class="truncate">{{ $p->name }}</span>
                            @if($p->group_name)
                                <span class="flex-none text-[10px] px-2 py-0.5 rounded-full bg-emerald-900/60 text-emerald-300 border border-emerald-800 font-medium">{{ $p->group_name }}</span>
                            @endif
                        </span>
                        @if($tournament->status === 'draft')
                            <button wire:click="removeParticipant({{ $p->id }})" wire:confirm="Hapus {{ $p->name }}?" class="text-red-500 hover:text-red-400 text-sm flex-none">
                                Hapus
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="text-center py-8 text-gray-500">Belum ada peserta. Tambahkan minimal 2 peserta.</p>
                @endforelse
            </div>

            <div class="mt-4 text-sm text-gray-500">
                Total: {{ $tournament->participants->count() }} peserta
            </div>
        </div>
    @endif

    {{-- Tab: Teams --}}
    @if($tab === 'teams')
        <div>
            @if($tournament->participants->count() >= 2 && $tournament->status === 'draft')
                <div class="mb-6 flex flex-wrap items-center gap-3">
                    @if($tournament->use_groups)
                        <button wire:click="generateTeams('random')" class="h-11 px-5 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                            <span>{{ $tournament->play_mode === 'singles' ? 'Generate Pemain' : 'Generate Acak (campur)' }}</span>
                        </button>
                        <button wire:click="generateTeams('byGroup')" class="h-11 px-5 inline-flex items-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-700 font-medium rounded-lg transition-colors text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.83z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                            <span>Generate Per Kelompok</span>
                        </button>
                        <p class="w-full text-xs text-gray-500">
                            @if($tournament->play_mode === 'singles')
                                1 pemain = 1 entri · Per Kelompok = semua pemain dalam kelompok jadi entri
                            @else
                                Acak = campur antar kelompok (fun) · Per Kelompok = pasang 2-2 dalam kelompok, 1 kelompok bisa jadi beberapa tim
                            @endif
                        </p>
                    @else
                        <button wire:click="generateTeams('random')" class="h-11 px-5 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                            <span>{{ $tournament->play_mode === 'singles' ? 'Generate Pemain' : 'Generate Tim (acak)' }}</span>
                        </button>
                    @endif
                </div>
            @elseif($tournament->participants->count() < 2)
                <p class="mb-6 text-sm text-gray-500">Minimal 2 peserta untuk generate tim.</p>
            @endif

            @if($tournament->use_groups && $tournament->status === 'draft' && $tournament->participants->count() >= 2 && $tournament->play_mode !== 'singles')
                @php
                    // Tim hasil generate acak tidak punya group_name → sembunyikan section manual
                    $randomGenerated = $tournament->teams->isNotEmpty()
                        && $tournament->teams->every(fn ($t) => blank($t->group_name));
                    $pairedIds = $tournament->teams->flatMap(fn ($t) => $t->members->pluck('id'))->all();
                    $pool = $tournament->participants
                        ->filter(fn ($p) => ! in_array($p->id, $pairedIds))
                        ->groupBy(fn ($p) => $p->group_name ?? 'Tanpa Kelompok');
                @endphp
                @if(! $randomGenerated)
                <div class="mb-6 px-4 py-3 bg-gray-800/50 rounded-lg border border-gray-700">
                    <h4 class="font-semibold text-sm text-emerald-400">✋ Atur Pasangan Manual</h4>
                    <p class="text-xs text-gray-500 mt-1">Klik 2 peserta dari kelompok yang sama untuk membuat tim. Klik peserta yang sama lagi untuk batal.</p>

                    @foreach($pool as $groupName => $members)
                        @if($members->isNotEmpty())
                            <div class="mt-3">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $groupName }}</div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($members as $p)
                                        <button wire:click="pairingClick({{ $p->id }})"
                                                class="px-3 h-9 rounded-full border text-sm transition-colors
                                                {{ $pairingParticipantId === $p->id
                                                    ? 'bg-emerald-600 border-emerald-500 text-white ring-2 ring-emerald-400/50'
                                                    : 'bg-gray-700/60 border-gray-600 text-gray-200 hover:bg-gray-600' }}">
                                            {{ $p->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($pool->isEmpty())
                        <p class="text-xs text-gray-500 mt-2">Semua peserta sudah berpasangan. 🎉</p>
                    @endif
                </div>
                @endif
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse ($tournament->teams->sortBy(fn ($t) => strtolower($t->name)) as $team)
                    <div class="px-4 py-3 bg-gray-800/50 rounded-lg border border-gray-700">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-semibold text-emerald-400">{{ $team->name }}</h4>
                                @if($team->group_name)
                                    <span class="inline-block mt-1 text-[11px] px-2 py-0.5 rounded-full bg-gray-700 text-gray-400">{{ $team->group_name }}</span>
                                @endif
                                <p class="text-sm text-gray-400 mt-1">
                                    {{ $team->members->pluck('name')->join(' & ') }}
                                </p>
                            </div>
                            @if($tournament->status === 'draft' && $team->group_name && $tournament->play_mode !== 'singles')
                                <button wire:click="unpairTeam({{ $team->id }})"
                                        class="shrink-0 w-8 h-8 rounded-full bg-gray-700/70 hover:bg-red-600/60 text-gray-400 hover:text-white transition-colors"
                                        title="Bongkar pasangan">
                                    ✕
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center py-8 text-gray-500 col-span-full">Belum ada tim. Generate tim dari peserta yang sudah ditambahkan.</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Tab: Bracket --}}
    @if($tab === 'bracket')
        <div>
            @if($tournament->status === 'draft' && $tournament->teams->count() >= 2)
                <div class="mb-6 flex flex-wrap items-center gap-3">
                    <button wire:click="generateBracket" class="h-11 px-5 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 010-5H6"/><path d="M18 9h1.5a2.5 2.5 0 000-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0012 0V2z"/></svg>
                        <span>Generate Bracket</span>
                    </button>
                    @if($tournament->gameMatches->count() > 0)
                        <p class="text-xs text-gray-500">
                            Generate ulang = susun ulang bracket (hasil pertandingan lama dihapus).
                        </p>
                    @endif
                </div>
            @endif

            @if($tournament->gameMatches->count() === 0)
                <div class="text-center py-12 text-gray-500">
                    <p class="text-4xl mb-3">🏸</p>
                    @if($tournament->teams->count() >= 2 && $tournament->status === 'draft')
                        <p>Bracket belum dibuat. Klik <span class="text-emerald-400">Generate Bracket</span> di atas.</p>
                    @else
                        <p>Generate tim & bracket terlebih dahulu.</p>
                    @endif
                </div>
            @else
                {{-- Bracket Display (connector layout) --}}
                <div class="overflow-x-auto pb-6">
                    <div class="relative mx-auto"
                         style="width: {{ $bracketLayout['width'] }}px; height: {{ $bracketLayout['height'] }}px;">

                        {{-- Round headers --}}
                        @foreach ($bracketLayout['rounds'] as $round => $matches)
                            <div class="absolute top-0 flex items-center justify-center"
                                 style="left: {{ $bracketLayout['roundLeft'][$round] }}px; width: 224px; height: {{ $bracketLayout['headerH'] }}px;">
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider text-center">
                                    {{ $bracketLayout['roundNames'][$round] ?? ('Ronde ' . $round) }}
                                </h3>
                            </div>
                        @endforeach

                        {{-- Match cards --}}
                        @foreach ($bracketLayout['rounds'] as $round => $matches)
                            <div class="absolute top-0" style="left: {{ $bracketLayout['roundLeft'][$round] }}px;">
                                @foreach ($matches as $match)
                                    <div class="absolute w-56 flex flex-col justify-center p-3 bg-gray-800 border rounded-lg
                                                {{ $match->status === 'ongoing' ? 'border-amber-600' : ($match->status === 'completed' ? 'border-emerald-800' : 'border-gray-700') }}"
                                         style="top: {{ $bracketLayout['tops'][$match->id] }}px; height: {{ $bracketLayout['cardH'] }}px;">
                                        {{-- Team 1 --}}
                                        <div class="flex items-center justify-between {{ $match->isTeam1Winner() ? 'text-emerald-400 font-semibold' : ($match->team1 ? 'text-gray-200' : 'text-gray-600') }}">
                                            <span class="text-sm truncate min-w-0 flex-1">
                                                @if($match->team1)
                                                    {{ $match->team1->name }}
                                                    @if($match->team1->members->isNotEmpty())
                                                        <span class="text-xs text-gray-500 ml-1">({{ $match->team1->membersList() }})</span>
                                                    @endif
                                                @elseif($match->isBye())
                                                    <span class="text-yellow-600">BYE</span>
                                                @else
                                                    —
                                                @endif
                                            </span>
                                            <span class="text-sm font-mono ml-2 flex-none">{{ $match->status !== 'pending' ? $match->score1 : '' }}</span>
                                        </div>
                                        {{-- VS --}}
                                        <div class="text-xs text-gray-600 my-1 text-center">VS</div>
                                        {{-- Team 2 --}}
                                        <div class="flex items-center justify-between {{ $match->isTeam2Winner() ? 'text-emerald-400 font-semibold' : ($match->team2 ? 'text-gray-200' : 'text-gray-600') }}">
                                            <span class="text-sm truncate min-w-0 flex-1">
                                                @if($match->team2)
                                                    {{ $match->team2->name }}
                                                    @if($match->team2->members->isNotEmpty())
                                                        <span class="text-xs text-gray-500 ml-1">({{ $match->team2->membersList() }})</span>
                                                    @endif
                                                @elseif($match->isBye())
                                                    <span class="text-yellow-600">BYE</span>
                                                @else
                                                    —
                                                @endif
                                            </span>
                                            <span class="text-sm font-mono ml-2 flex-none">{{ $match->status !== 'pending' ? $match->score2 : '' }}</span>
                                        </div>

                                        {{-- Actions --}}
                                        @if($match->status === 'pending' && $match->team1_id && $match->team2_id && $tournament->status === 'ongoing')
                                            <button wire:click="startMatch({{ $match->id }})" class="mt-2 w-full h-10 text-sm font-medium bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors">
                                                Mulai
                                            </button>
                                        @endif

                                        @if($match->status === 'ongoing')
                                            <a href="{{ route('scoreboard.show', $match->id) }}"
                                               class="block mt-2 w-full h-10 flex items-center justify-center gap-1.5 text-sm font-medium bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-700/60 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                                Scoreboard
                                            </a>
                                        @endif

                                        @if($match->status === 'completed' && $match->winner)
                                            <div class="mt-2 text-xs {{ $match->isBye() ? 'text-yellow-500' : 'text-emerald-500' }} text-center">
                                                @if($match->isBye())
                                                    ↪ {{ $match->winner->name }} (BYE)
                                                @else
                                                    ✓ {{ $match->winner->name }} menang
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Edit Score Modal inline --}}
                                        @if($updatingMatchId === $match->id)
                                            <div class="mt-3 p-3 bg-gray-900 rounded-lg border border-gray-600">
                                                <div class="flex gap-2 items-center mb-2">
                                                    <div class="flex-1">
                                                        <label class="text-xs text-gray-400">{{ $match->team1->name ?? 'Team 1' }}</label>
                                                        <input wire:model="score1" type="number" min="0" class="w-full px-2 py-1 bg-gray-800 border border-gray-700 rounded text-sm text-center">
                                                    </div>
                                                    <span class="text-gray-500 text-xs mt-5">:</span>
                                                    <div class="flex-1">
                                                        <label class="text-xs text-gray-400">{{ $match->team2->name ?? 'Team 2' }}</label>
                                                        <input wire:model="score2" type="number" min="0" class="w-full px-2 py-1 bg-gray-800 border border-gray-700 rounded text-sm text-center">
                                                    </div>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button wire:click="saveScore" class="flex-1 h-10 text-sm font-medium bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg">Simpan</button>
                                                    <button wire:click="cancelEdit" class="flex-1 h-10 text-sm font-medium bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg">Batal</button>
                                                </div>
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
            @endif
        </div>
    @endif

    {{-- Modal konfirmasi: mode diubah → langsung generate tim/pemain --}}
    @if($showGenerateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="mode-change-modal">
            <div class="absolute inset-0 bg-black/70" wire:click="$set('showGenerateModal', false)"></div>
            <div class="relative w-full max-w-md sm:max-w-lg rounded-xl bg-gray-900 border border-gray-700 p-6 shadow-2xl">
                <h3 class="text-lg font-semibold text-gray-100">
                    Mode diubah ke {{ $tournament->playModeLabel() }}
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    @if($tournament->play_mode === 'singles')
                        Mau langsung generate pemain? Setiap peserta akan jadi 1 entri (1 pemain = 1 tim).
                    @else
                        Mau langsung generate tim? Peserta akan dipasangkan secara acak.
                    @endif
                    Tim & bracket lama akan dihapus.
                </p>
                <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:gap-3">
                    <button wire:click="confirmGenerateAfterModeChange"
                            class="h-12 sm:h-11 sm:flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                        {{ $tournament->play_mode === 'singles' ? 'Generate Pemain' : 'Generate Tim' }}
                    </button>
                    <button wire:click="$set('showGenerateModal', false)"
                            class="h-12 sm:h-11 sm:flex-1 bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium rounded-lg transition-colors text-sm">
                        Nanti saja
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
