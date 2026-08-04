<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Turnamen</h1>
        <div class="text-sm text-gray-400">
            {{ $tournaments->total() }} {{ $showArchived ? 'diarsipkan' : 'total' }}
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b border-gray-700">
        <button wire:click="showActive" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ !$showArchived ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Aktif
        </button>
        <button wire:click="showArchive" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $showArchived ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Arsip
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="mb-6 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-lg text-emerald-200 text-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- Create Form (only in active tab) --}}
    @if(!$showArchived)
    <form wire:submit="create" class="mb-8 space-y-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                wire:model="newName"
                type="text"
                placeholder="Nama turnamen baru..."
                class="flex-1 h-11 px-4 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
            <button
                type="submit"
                class="px-6 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors"
            >
                + Buat
            </button>
        </div>
        @error('newName') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror

        {{-- Opsi: maksimal peserta + kelompok (opsional) --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 px-4 py-3 bg-gray-800/40 border border-gray-700 rounded-lg">
            <label class="flex items-center gap-2 text-sm text-gray-300">
                <span class="text-gray-500">Maksimal peserta:</span>
                <select wire:model="newMax" class="bg-gray-800 text-gray-200 border border-gray-700 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">Tanpa batas</option>
                    @foreach([4, 8, 16, 32, 64, 128] as $n)
                        <option value="{{ $n }}">{{ $n }} peserta</option>
                    @endforeach
                </select>
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer select-none">
                <input type="checkbox" wire:model.live="newUseGroups"
                       class="w-4 h-4 rounded bg-gray-900 border-gray-700 text-emerald-500 focus:ring-emerald-500/30">
                Peserta dari beberapa kelompok
            </label>

            @if($newUseGroups)
                <div class="w-full space-y-2">
                    @forelse($newGroupNames as $i => $groupName)
                        <div class="flex items-center gap-2">
                            <input
                                wire:model="newGroupNames.{{ $i }}"
                                type="text"
                                placeholder="Nama kelompok {{ $i + 1 }} (mis. Grup A)"
                                class="flex-1 h-11 min-w-0 px-3 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            >
                            <button type="button" wire:click="addGroupName"
                                    class="w-11 h-11 flex-none flex items-center justify-center bg-gray-800 hover:bg-gray-700 text-emerald-300 border border-emerald-800 rounded-lg transition-colors text-lg"
                                    title="Tambah kelompok">+</button>
                            @if(count($newGroupNames) > 1)
                                <button type="button" wire:click="removeGroupName({{ $i }})"
                                        class="w-11 h-11 flex-none flex items-center justify-center text-gray-400 hover:text-red-400 rounded-lg transition-colors"
                                        title="Hapus kelompok">✕</button>
                            @endif
                        </div>
                    @empty
                        <div class="flex items-center gap-2">
                            <input
                                wire:model="newGroupNames.0"
                                type="text"
                                placeholder="Nama kelompok 1 (mis. Grup A)"
                                class="flex-1 h-11 min-w-0 px-3 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            >
                            <button type="button" wire:click="addGroupName"
                                    class="w-11 h-11 flex-none flex items-center justify-center bg-gray-800 hover:bg-gray-700 text-emerald-300 border border-emerald-800 rounded-lg transition-colors text-lg"
                                    title="Tambah kelompok">+</button>
                        </div>
                    @endforelse
                </div>
                <p class="w-full text-xs text-gray-500">
                    @if($newMax && count($newGroupNames))
                        {{ $newMax }} peserta ÷ {{ count($newGroupNames) }} kelompok = <span class="text-emerald-400">±{{ ceil($newMax / count($newGroupNames)) }} peserta per kelompok</span> (rata)
                    @else
                        Pilih maksimal peserta untuk melihat kuota per kelompok.
                    @endif
                </p>
            @endif
            @error('newGroupNames') <p class="w-full text-xs text-red-400">{{ $message }}</p> @enderror
        </div>
    </form>
    @endif

    {{-- Tournament List --}}
    <div class="space-y-3">
        @forelse ($tournaments as $t)
            <a
                href="{{ route('tournaments.show', $t) }}"
                class="block px-6 py-4 bg-gray-800 border border-gray-700 rounded-lg hover:border-gray-600 transition-colors group"
            >
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold truncate group-hover:text-emerald-400 transition-colors">{{ $t->name }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $t->participants_count ?? 0 }} peserta
                            &middot; {{ $t->teams_count ?? 0 }} tim
                            @if($showArchived && $t->original_status)
                                &middot; {{ $t->original_status }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-none">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap
                            @if($t->status === 'archived') bg-gray-700/50 text-gray-500 border border-gray-600
                            @elseif($t->status === 'draft') bg-gray-700 text-gray-400
                            @elseif($t->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                            @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                            @endif
                        ">
                            {{ $t->statusLabel() }}
                        </span>
                        <span class="text-gray-600 group-hover:text-gray-400">→</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-16 text-gray-500">
                <p class="text-5xl mb-4">🏸</p>
                <p>
                    @if($showArchived)
                        Belum ada turnamen yang diarsipkan.
                    @else
                        Belum ada turnamen. Buat yang pertama!
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tournaments->links() }}
    </div>
</div>
