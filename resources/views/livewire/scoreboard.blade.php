<div class="h-dvh w-screen flex flex-col overflow-hidden"
     x-data="{
          isFullscreen: !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement),
          toggleFullscreen() {
              if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                  let exit = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
                  if (exit) exit.call(document);
              } else {
                  let el = document.documentElement;
                  let req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                  if (req) {
                      let p = req.call(el);
                      if (p && p.then) {
                          p.then(() => {
                              if (screen.orientation && screen.orientation.lock)
                                  screen.orientation.lock('landscape').catch(() => {});
                          }).catch(() => {});
                      }
                  }
              }
          },
          init() {
              let update = () => {
                  this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
              };
              document.addEventListener('fullscreenchange', update);
              document.addEventListener('webkitfullscreenchange', update);
              document.addEventListener('mozfullscreenchange', update);
              document.addEventListener('MSFullscreenChange', update);
          }@if(!$readonly),
          timer: null,
          isLongPress: false,
          cooldown: false,
          clickTeam(team) {
              if (this.isLongPress) return;
              if (this.cooldown) return;
              this.cooldown = true;
              $wire.increment(team);
              setTimeout(() => { this.cooldown = false; }, 400);
          },
          startLongPress(team) {
              if (this.cooldown) return;
              this.isLongPress = false;
              this.timer = setTimeout(() => {
                  this.isLongPress = true;
                  $wire.decrement(team);
              }, 600);
          },
          endLongPress() {
              clearTimeout(this.timer);
              this.timer = null;
              setTimeout(() => { this.isLongPress = false; }, 50);
          }
     @endif
      }"
     wire:poll.2000ms="$refresh"
     @if(!$readonly && $controlActive)
     x-init="setInterval(() => { if ($wire) $wire.heartbeat(); }, 5000)"
     @endif
     >

    {{-- Overlay: scoreboard ini dikunci device lain (admin control) --}}
    @if(!$readonly && $lockedByOther)
    <div class="fixed inset-0 z-50 bg-black/85 flex flex-col items-center justify-center p-8 text-center"
         wire:poll.2000ms="$refresh">
        <div class="text-6xl mb-4">🔒</div>
        <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">Scoreboard sedang dikendalikan</h2>
        <p class="text-gray-400 text-sm max-w-sm">
            Device lain sedang mengoperasikan scoreboard ini.<br>
            Buka dari device yang sama untuk mengubah skor.
        </p>
        <a href="/admin/tournaments/{{ $match->tournament_id }}"
           class="mt-6 px-6 h-12 inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors">
            ← Kembali ke Turnamen
        </a>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- PANEL ATAS: Black — info, game status, games won            --}}
    {{-- ============================================================ --}}
    <div class="flex-none h-14 bg-black flex items-center gap-3 z-10 header-safe">
        {{-- Back --}}
        <a href="{{ $closeUrl ?? '' }}"
           class="flex-none w-11 h-11 flex items-center justify-center
                   bg-white/10 hover:bg-white/20 text-white/70 hover:text-white
                   rounded-xl transition-all no-underline active:scale-95">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/>
                <path d="M12 19l-7-7 7-7"/>
            </svg>
        </a>

        {{-- Fullscreen --}}
        <button @click="toggleFullscreen()"
                class="flex-none w-11 h-11 flex items-center justify-center
                       bg-white/10 hover:bg-white/20 text-white/70 hover:text-white
                       rounded-xl transition-all active:scale-95"
                :title="isFullscreen ? 'Keluar fullscreen' : 'Fullscreen landscape'">
            <svg x-show="!isFullscreen" x-cloak class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 00-2 2v3"/>
                <path d="M21 8V5a2 2 0 00-2-2h-3"/>
                <path d="M3 16v3a2 2 0 002 2h3"/>
                <path d="M16 21h3a2 2 0 002-2v-3"/>
            </svg>
            <svg x-show="isFullscreen" x-cloak class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3v3a2 2 0 01-2 2H3"/>
                <path d="M21 8h-3a2 2 0 01-2-2V3"/>
                <path d="M3 16h3a2 2 0 012 2v3"/>
                <path d="M16 21v-3a2 2 0 012-2h3"/>
            </svg>
        </button>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Tournament name --}}
        <h1 class="text-xs sm:text-sm font-bold text-white/90 truncate max-w-[40vw] sm:max-w-none">🏸 {{ $tournamentName }}</h1>
        <span class="text-[10px] text-gray-500 hidden sm:inline whitespace-nowrap">
            · Ronde {{ $match->round }}@if($match->match_number) · Match {{ $match->match_number }}@endif
            @if($match->next_match_id === null)<span class="text-amber-500/70 ml-1">(Final)</span>@endif
        </span>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Game label + LIVE --}}
        <div class="flex-none flex items-center gap-1.5">
            @if($matchOver)
                <span class="text-[10px] px-2 py-0.5 bg-emerald-600/30 text-emerald-300 border border-emerald-600/50 rounded-full font-semibold whitespace-nowrap">
                    MATCH SELESAI
                </span>
            @else
                <span class="text-[10px] px-2 py-0.5 bg-amber-600/30 text-amber-300 border border-amber-600/50 rounded-full font-semibold whitespace-nowrap">
                    {{ $gameLabel }}
                </span>
                <span class="flex items-center gap-1 text-[10px] text-red-400">
                    <span class="inline-block w-1.5 h-1.5 bg-red-500 rounded-full" style="animation: pulse 1.5s infinite;"></span>
                    LIVE
                </span>
            @endif
        </div>

        {{-- Game history dots --}}
        @if($gamesToWin > 1 && count($previousGames) > 1)
            <div class="flex-none flex items-center gap-1.5 ml-2 pl-2 border-l border-white/10">
                @foreach($previousGames as $game)
                    @if($game['winner'] === 1)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-1 ring-emerald-400/30"
                              title="Game {{ $game['game'] }}: {{ $game['score1'] }} - {{ $game['score2'] }}"></span>
                    @elseif($game['winner'] === 2)
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-1 ring-amber-400/30"
                              title="Game {{ $game['game'] }}: {{ $game['score1'] }} - {{ $game['score2'] }}"></span>
                    @elseif($loop->last)
                        <span class="w-2.5 h-2.5 rounded-full border-2 border-white/40" style="animation: pulse 1.5s infinite;"
                              title="Game {{ $game['game'] }} sedang berlangsung"></span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full border border-gray-600 opacity-30"
                              title="Game {{ $game['game'] }}"></span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- SCORE CARD — takes ~70% of screen, scores fill the space      --}}
    {{-- ============================================================ --}}
    <div class="flex-1 bg-[#4ade80] flex flex-col items-center justify-center min-h-0 p-1.5 relative">

        {{-- Dark card: stretches to fill green panel but capped at 80% height --}}
        <div class="bg-[#1e1e2e] rounded-2xl shadow-2xl w-full h-full max-h-[80%]
                    flex flex-col items-center justify-center p-4 sm:p-8 gap-1">

            {{-- SCOREBOARD title --}}
            <h2 class="text-center text-[9px] uppercase tracking-[0.3em] font-semibold text-gray-500 flex-none hidden sm:block">
                SCOREBOARD
            </h2>

            {{-- Map teams to left/right based on court flip --}}
            @php
                $leftTeam = $courtFlipped ? $match->team2 : $match->team1;
                $rightTeam = $courtFlipped ? $match->team1 : $match->team2;
                $leftScore = $courtFlipped ? ($scores[1] ?? 0) : ($scores[0] ?? 0);
                $rightScore = $courtFlipped ? ($scores[0] ?? 0) : ($scores[1] ?? 0);
                $leftTeamNum = $courtFlipped ? 2 : 1;
                $rightTeamNum = $courtFlipped ? 1 : 2;
                $leftIsWinner = $courtFlipped ? ($matchWinner === 2) : ($matchWinner === 1);
                $rightIsWinner = $courtFlipped ? ($matchWinner === 1) : ($matchWinner === 2);
                $leftName = $leftTeam?->members?->pluck('name')->join(' & ') ?: ($leftTeam?->name ?? ($courtFlipped ? 'Team B' : 'Team A'));
                $rightName = $rightTeam?->members?->pluck('name')->join(' & ') ?: ($rightTeam?->name ?? ($courtFlipped ? 'Team A' : 'Team B'));
                $leftGamesWon = $courtFlipped ? ($gamesWon[1] ?? 0) : ($gamesWon[0] ?? 0);
                $rightGamesWon = $courtFlipped ? ($gamesWon[0] ?? 0) : ($gamesWon[1] ?? 0);
                $clickable = !$readonly && !$matchOver && !$showSwitchCourt;
            @endphp

            {{-- Scores: flex row --}}
            <div class="flex-1 w-full flex flex-row items-stretch justify-center gap-3 sm:gap-8 py-2 sm:py-6">

                {{-- LEFT SIDE (sensitive to court flip) --}}
                <div @if($clickable)
                     @mousedown.prevent="startLongPress({{ $leftTeamNum }})"
                     @mouseup.prevent="clickTeam({{ $leftTeamNum }}); endLongPress()"
                     @mouseleave.prevent="endLongPress()"
                     @touchstart.prevent="startLongPress({{ $leftTeamNum }})"
                     @touchend.prevent="clickTeam({{ $leftTeamNum }}); endLongPress()"
                     @endif
                     class="flex-1 flex flex-col items-center justify-center
                            @if($clickable) cursor-pointer select-none @endif rounded-xl transition-colors duration-150
                            {{ $matchOver || !$clickable ? 'opacity-60 pointer-events-none' : 'hover:bg-white/5' }}
                            {{ $leftIsWinner ? 'ring-2 ring-emerald-400 ring-offset-2 ring-offset-[#1e1e2e]' : '' }}
                            @if($clickable) :class="cooldown ? 'bg-white/5 scale-[0.97]' : ''" @endif">

                    {{-- Score number --}}
                    <div class="font-bold text-white tabular-nums leading-none text-center"
                         style="font-size: min(35vmin, 16rem)">
                        {{ $leftScore }}
                    </div>

                    {{-- Team name --}}
                    <h3 class="font-bold tracking-wide mt-1 text-center"
                        style="font-size: min(3.5vmin, 1.5rem); color: {{ $leftIsWinner ? '#4ade80' : '#22c55e' }};">
                        {{ $leftName }}
                    </h3>

                    @if($matchOver && $leftIsWinner)
                        <div class="text-[2.5vw] sm:text-sm text-emerald-400 font-semibold mt-0.5">🏆 MENANG</div>
                    @elseif($clickable)
                        <div class="text-[2vw] sm:text-[10px] text-gray-600 font-medium hidden sm:block mt-0.5">+1 klik · -1 tahan</div>
                    @endif
                </div>

                {{-- RIGHT SIDE --}}
                <div @if($clickable)
                     @mousedown.prevent="startLongPress({{ $rightTeamNum }})"
                     @mouseup.prevent="clickTeam({{ $rightTeamNum }}); endLongPress()"
                     @mouseleave.prevent="endLongPress()"
                     @touchstart.prevent="startLongPress({{ $rightTeamNum }})"
                     @touchend.prevent="clickTeam({{ $rightTeamNum }}); endLongPress()"
                     @endif
                     class="flex-1 flex flex-col items-center justify-center
                            @if($clickable) cursor-pointer select-none @endif rounded-xl transition-colors duration-150
                            {{ $matchOver || !$clickable ? 'opacity-60 pointer-events-none' : 'hover:bg-white/5' }}
                            {{ $rightIsWinner ? 'ring-2 ring-emerald-400 ring-offset-2 ring-offset-[#1e1e2e]' : '' }}
                            @if($clickable) :class="cooldown ? 'bg-white/5 scale-[0.97]' : ''" @endif">

                    {{-- Score number --}}
                    <div class="font-bold text-white tabular-nums leading-none text-center"
                         style="font-size: min(35vmin, 16rem)">
                        {{ $rightScore }}
                    </div>

                    {{-- Team name --}}
                    <h3 class="font-bold tracking-wide mt-1 text-center"
                        style="font-size: min(3.5vmin, 1.5rem); color: {{ $rightIsWinner ? '#4ade80' : '#22c55e' }};">
                        {{ $rightName }}
                    </h3>

                    @if($matchOver && $rightIsWinner)
                        <div class="text-[2.5vw] sm:text-sm text-emerald-400 font-semibold mt-0.5">🏆 MENANG</div>
                    @elseif($clickable)
                        <div class="text-[2vw] sm:text-[10px] text-gray-600 font-medium hidden sm:block mt-0.5">+1 klik · -1 tahan</div>
                    @endif
                </div>

            </div>{{-- /scores flex row --}}

            {{-- Match over info --}}
            @if($matchOver && !$showSwitchCourt)
                <div class="text-center flex-none">
                    <p class="text-emerald-400 font-bold text-sm">
                        {{ $leftIsWinner ? $leftName : $rightName }} menang!
                    </p>
                    <p class="text-gray-500 text-xs mt-0.5">
                        {{ $leftGamesWon }} - {{ $rightGamesWon }}
                    </p>
                </div>
            @endif

        </div>{{-- /dark card --}}

        {{-- Switch Court Overlay --}}
        @if($showSwitchCourt)
        <div class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center rounded-2xl z-20 p-8"
             wire:click="confirmSwitchCourt"
             style="cursor: pointer">
            <div class="text-6xl mb-4">🔄</div>
            <h2 class="text-2xl font-bold text-white mb-2 text-center">Pindah Lapangan</h2>
            <p class="text-gray-400 text-sm mb-6 text-center">
                Game {{ $currentGame + 1 }} selesai<br>
                Ketuk untuk pindah sisi lapangan
            </p>
            <button wire:click.stop="confirmSwitchCourt"
                    class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-400 text-white font-bold text-lg rounded-xl transition-colors active:scale-95">
                🔄 Pindah Lapangan
            </button>
        </div>
        @endif

    </div>

    <style>
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        [x-cloak] { display: none !important; }
        .header-safe {
            padding-left: calc(2.5rem + env(safe-area-inset-left, 0px));
            padding-right: calc(2.5rem + env(safe-area-inset-right, 0px));
        }
    </style>
</div>
