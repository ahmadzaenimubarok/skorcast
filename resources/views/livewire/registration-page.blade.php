<div wire:poll.4s="refresh">
    {{-- Header Card --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 text-center">
        <div class="text-4xl mb-2">🏸</div>
        <h1 class="text-2xl font-bold tracking-tight break-words">{{ $tournament->name }}</h1>
        <div class="mt-3 flex items-center justify-center gap-2">
            <span class="text-xs px-3 py-1 rounded-full font-medium
                @if($tournament->status === 'draft') bg-emerald-900/50 text-emerald-300 border border-emerald-700
                @elseif($tournament->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                @else bg-gray-800 text-gray-400 border border-gray-700
                @endif
            ">
                @if($tournament->status === 'draft') Buka Pendaftaran
                @elseif($tournament->status === 'ongoing') LIVE
                @else Selesai
                @endif
            </span>
        </div>

        <a href="{{ route('public.bracket', $tournament->code) }}"
           class="mt-5 block w-full py-3.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-200 hover:text-white font-semibold rounded-xl transition-all text-base">
            Lihat Halaman Publik ↗
        </a>

        <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
            <div class="bg-gray-800/50 rounded-xl px-4 py-3">
                <div class="text-gray-500 text-xs mb-0.5">Format</div>
                <div class="font-medium">{{ $tournament->games_to_win === 1 ? '1 Game' : 'Best of 3' }}</div>
            </div>
            <div class="bg-gray-800/50 rounded-xl px-4 py-3">
                <div class="text-gray-500 text-xs mb-0.5">Peserta</div>
                <div class="font-medium">{{ $tournament->participants->count() }} orang</div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="mt-4 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-200 text-sm">
            ✅ {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mt-4 px-4 py-3 bg-red-900/50 border border-red-700 rounded-xl text-red-200 text-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Registration Form --}}
    @if($tournament->status === 'draft')
        @php
            $registrationFull = $tournament->max_participants && $tournament->participants->count() >= $tournament->max_participants;
        @endphp
        <div class="mt-4 bg-gray-900 rounded-2xl border border-gray-800 p-6">
            <h2 class="font-semibold text-lg mb-1">Daftar Sekarang</h2>

            {{-- Progress kuota --}}
            @if($tournament->max_participants)
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-600 rounded-full transition-all"
                             style="width: {{ min(100, ($tournament->participants->count() / $tournament->max_participants) * 100) }}%"></div>
                    </div>
                    <span class="text-sm text-gray-400 flex-none">{{ $tournament->participants->count() }}/{{ $tournament->max_participants }} terdaftar</span>
                </div>
            @endif

            @if($registrationFull)
                <div class="text-center py-4">
                    <p class="text-3xl mb-2">🈵</p>
                    <p class="font-medium">Kuota sudah penuh</p>
                    <p class="text-sm text-gray-500 mt-1">Mohon maaf, pendaftaran ditutup karena kuota penuh.</p>
                </div>
            @else
            <p class="text-sm text-gray-500 mb-4">Masukkan nama kamu, lalu tekan daftar.</p>
            <form wire:submit="register" class="flex flex-col gap-3">
                <input
                    wire:model="name"
                    type="text"
                    placeholder="Nama lengkap..."
                    autocomplete="name"
                    class="w-full px-4 py-3.5 bg-gray-800 border border-gray-600 rounded-xl text-gray-100 placeholder-gray-500 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                @if($tournament->use_groups)
                    <select wire:model="group" class="w-full px-4 py-3.5 bg-gray-800 border border-gray-600 rounded-xl text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-base">
                        <option value="">Pilih kelompok...</option>
                        @foreach($tournament->groupOptions() as $key => $label)
                            @php
                                $filled = $tournament->participants->where('group_name', $label)->count();
                                $cap = $tournament->groupCapacity();
                            @endphp
                            <option value="{{ $key }}" @if($cap && $filled >= $cap) disabled @endif>
                                {{ $label }}@if($cap) ({{ $filled }}/{{ $cap }})@endif
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('name') <span class="text-xs text-red-400 -mt-1">{{ $message }}</span> @enderror
                <button type="submit"
                        class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-semibold rounded-xl transition-all text-base">
                    Daftar
                </button>
            </form>
            @endif
        </div>
    @else
        <div class="mt-4 bg-gray-900 rounded-2xl border border-gray-800 p-6 text-center">
            <p class="text-3xl mb-2">🔒</p>
            <p class="font-medium">Pendaftaran ditutup</p>
            <p class="text-sm text-gray-500 mt-1">Turnamen sudah dimulai, pendaftaran peserta tidak bisa lagi.</p>
        </div>
    @endif

    {{-- Participants List --}}
    <div class="mt-4 bg-gray-900 rounded-2xl border border-gray-800 p-6">
        <h2 class="font-semibold text-lg mb-4">Peserta Terdaftar</h2>
        @forelse ($tournament->participants as $p)
            <div class="flex items-center gap-3 py-2.5 border-b border-gray-800/70 last:border-b-0">
                <span class="flex-none w-7 h-7 rounded-full bg-gray-800 text-gray-400 text-xs flex items-center justify-center font-semibold">
                    {{ $loop->iteration }}
                </span>
                <span class="text-gray-100">{{ $p->name }}</span>
            </div>
        @empty
            <p class="text-center py-6 text-gray-500 text-sm">Belum ada peserta. Jadilah yang pertama! 🎉</p>
        @endforelse
    </div>

    <p class="text-center text-xs text-gray-700 mt-6">
        🏸 Skor Cast &middot; skorcast.online
    </p>
</div>
