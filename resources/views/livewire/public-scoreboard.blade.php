<div class="h-dvh w-screen flex flex-col overflow-hidden"
     x-data="scoreboardLocal()"
     x-init="init()"
     x-cloak>

    {{-- ============================================================ --}}
    {{-- PANEL ATAS: Black — info, game status, games won            --}}
    {{-- ============================================================ --}}
    <div class="flex-none h-14 bg-black flex items-center gap-3 z-10 header-safe">
        {{-- Fullscreen --}}
        <button @click="toggleFullscreen()"
                class="flex-none w-11 h-11 flex items-center justify-center
                       bg-white/10 hover:bg-white/20 text-white/70 hover:text-white
                       rounded-xl transition-all active:scale-95"
                :title="isFullscreen ? 'Keluar fullscreen' : 'Fullscreen landscape'">
            <svg x-show="!isFullscreen" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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

        {{-- Reset --}}
        <button @click="if (confirm('Reset scoreboard? Semua skor akan hilang.')) resetBoard()"
                class="flex-none w-11 h-11 flex items-center justify-center
                       bg-white/10 hover:bg-white/20 text-white/70 hover:text-white
                       rounded-xl transition-all active:scale-95"
                title="Reset scoreboard">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                <path d="M3 3v5h5"/>
            </svg>
        </button>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Title --}}
        <h1 class="min-w-0 text-xs sm:text-sm font-bold text-white/90 truncate">🏸 Scoreboard</h1>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Game label + LIVE --}}
        <div class="flex-none flex items-center gap-1.5">
            <template x-if="matchOver">
                <span class="text-[10px] px-2 py-0.5 bg-emerald-600/30 text-emerald-300 border border-emerald-600/50 rounded-full font-semibold whitespace-nowrap">
                    MATCH SELESAI
                </span>
            </template>
            <template x-if="!matchOver">
                <span class="flex-none flex items-center gap-1.5">
                    <span class="text-[10px] px-2 py-0.5 bg-amber-600/30 text-amber-300 border border-amber-600/50 rounded-full font-semibold whitespace-nowrap" x-text="gameLabel"></span>
                    <span class="flex items-center gap-1 text-[10px] text-red-400">
                        <span class="inline-block w-1.5 h-1.5 bg-red-500 rounded-full" style="animation: pulse 1.5s infinite;"></span>
                        LIVE
                    </span>
                </span>
            </template>
        </div>

        {{-- Game history dots --}}
        <template x-if="gamesToWin > 1 && previousGames.length > 1">
            <div class="flex-none flex items-center gap-1.5 ml-2 pl-2 border-l border-white/10">
                <template x-for="game in previousGames" :key="game.game">
                    <span class="w-2.5 h-2.5 rounded-full"
                          :class="{
                              'bg-emerald-400 ring-1 ring-emerald-400/30': game.winner === 1,
                              'bg-amber-400 ring-1 ring-amber-400/30': game.winner === 2,
                              'border-2 border-white/40 animate-pulse-fast': (game.winner === null && game.game === previousGames[previousGames.length-1].game),
                              'border border-gray-600 opacity-30': (game.winner === null && game.game !== previousGames[previousGames.length-1].game)
                          }"
                          :title="'Game ' + game.game + ': ' + game.score1 + ' - ' + game.score2"></span>
                </template>
            </div>
        </template>
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

            {{-- Scores: flex row --}}
            <div class="flex-1 w-full flex flex-row items-stretch justify-center gap-3 sm:gap-8 py-2 sm:py-6">

                {{-- LEFT SIDE (sensitive to court flip) --}}
                <div @mousedown.prevent="if(clickable) startLongPress(1, $event)"
                     @mouseup.prevent="if(clickable) clickTeam(1, $event); endLongPress($event)"
                     @mouseleave.prevent="if(clickable) endLongPress()"
                     @touchstart.prevent="if(clickable) startLongPress(1, $event)"
                     @touchend.prevent="if(clickable) clickTeam(1, $event); endLongPress($event)"
                     class="flex-1 flex flex-col items-center justify-center
                            rounded-xl transition-colors duration-150"
                     :class="{
                         'cursor-pointer select-none hover:bg-white/5': clickable,
                         'opacity-60 pointer-events-none': (matchOver || !clickable),
                         'ring-2 ring-emerald-400 ring-offset-2 ring-offset-[#1e1e2e]': leftIsWinner
                         }">

                    {{-- Score number --}}
                    <div class="font-bold text-white tabular-nums leading-none text-center"
                         style="font-size: min(35vmin, 16rem)"
                         x-text="leftScore"></div>

                    {{-- Player name (editable) --}}
                    <input type="text" x-model="nameA" @input="save()"
                           class="font-bold tracking-wide mt-1 text-center bg-transparent border-b border-transparent focus:border-white/30 outline-none min-w-0 text-center"
                           :style="'font-size: min(3.5vmin, 1.5rem); color: ' + (leftIsWinner ? '#4ade80' : '#22c55e') + ';'"
                           maxlength="24" />

                    <template x-if="matchOver && leftIsWinner">
                        <div class="text-[2.5vw] sm:text-sm text-emerald-400 font-semibold mt-0.5">🏆 MENANG</div>
                    </template>
                    <template x-if="clickable && !(matchOver && leftIsWinner)">
                        <div class="text-[2vw] sm:text-[10px] text-gray-600 font-medium hidden sm:block mt-0.5">+1 klik · -1 tahan</div>
                    </template>
                </div>

                {{-- RIGHT SIDE --}}
                <div @mousedown.prevent="if(clickable) startLongPress(2, $event)"
                     @mouseup.prevent="if(clickable) clickTeam(2, $event); endLongPress($event)"
                     @mouseleave.prevent="if(clickable) endLongPress()"
                     @touchstart.prevent="if(clickable) startLongPress(2, $event)"
                     @touchend.prevent="if(clickable) clickTeam(2, $event); endLongPress($event)"
                     class="flex-1 flex flex-col items-center justify-center
                            rounded-xl transition-colors duration-150"
                     :class="{
                         'cursor-pointer select-none hover:bg-white/5': clickable,
                         'opacity-60 pointer-events-none': (matchOver || !clickable),
                         'ring-2 ring-emerald-400 ring-offset-2 ring-offset-[#1e1e2e]': rightIsWinner
                         }">

                    {{-- Score number --}}
                    <div class="font-bold text-white tabular-nums leading-none text-center"
                         style="font-size: min(35vmin, 16rem)"
                         x-text="rightScore"></div>

                    {{-- Player name (editable) --}}
                    <input type="text" x-model="nameB" @input="save()"
                           class="font-bold tracking-wide mt-1 text-center bg-transparent border-b border-transparent focus:border-white/30 outline-none min-w-0 text-center"
                           :style="'font-size: min(3.5vmin, 1.5rem); color: ' + (rightIsWinner ? '#4ade80' : '#22c55e') + ';'"
                           maxlength="24" />

                    <template x-if="matchOver && rightIsWinner">
                        <div class="text-[2.5vw] sm:text-sm text-emerald-400 font-semibold mt-0.5">🏆 MENANG</div>
                    </template>
                    <template x-if="clickable && !(matchOver && rightIsWinner)">
                        <div class="text-[2vw] sm:text-[10px] text-gray-600 font-medium hidden sm:block mt-0.5">+1 klik · -1 tahan</div>
                    </template>
                </div>

            </div>{{-- /scores flex row --}}

            {{-- Match over info --}}
            <template x-if="matchOver && !showSwitchCourt">
                <div class="text-center flex-none">
                    <p class="text-emerald-400 font-bold text-sm" x-text="(leftIsWinner ? leftName : rightName) + ' menang!'"></p>
                    <p class="text-gray-500 text-xs mt-0.5" x-text="leftGamesWon + ' - ' + rightGamesWon"></p>
                </div>
            </template>

            {{-- Games-to-win selector (saat match belum selesai & belum mulai game 2+) --}}
            <template x-if="!matchOver && detail.length === 1 && detail[0].t1 === 0 && detail[0].t2 === 0">
                <div class="flex-none mt-2 flex items-center gap-2 text-[11px] text-gray-500">
                    <span>Format:</span>
                    <button @click="setGamesToWin(1)"
                            class="px-2.5 h-7 rounded-lg border transition-colors text-xs"
                            :class="gamesToWin === 1 ? 'bg-emerald-600 border-emerald-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300 hover:bg-gray-700'">1 Game</button>
                    <button @click="setGamesToWin(2)"
                            class="px-2.5 h-7 rounded-lg border transition-colors text-xs"
                            :class="gamesToWin === 2 ? 'bg-emerald-600 border-emerald-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-300 hover:bg-gray-700'">Best of 3</button>
                </div>
            </template>
        </div>{{-- /dark card --}}

        {{-- Switch Court Overlay --}}
        <template x-if="showSwitchCourt">
            <div class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center rounded-2xl z-20 p-8"
                 @click="confirmSwitchCourt()"
                 style="cursor: pointer">
                <div class="text-6xl mb-4">🔄</div>
                <h2 class="text-2xl font-bold text-white mb-2 text-center">Pindah Lapangan</h2>
                <p class="text-gray-400 text-sm mb-6 text-center">
                    Game <span x-text="currentGame + 1"></span> selesai<br>
                    Ketuk untuk pindah sisi lapangan
                </p>
                <button @click.stop="confirmSwitchCourt()"
                        class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-400 text-white font-bold text-lg rounded-xl transition-colors active:scale-95">
                    🔄 Pindah Lapangan
                </button>
            </div>
        </template>

    </div>

    <style>
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .animate-pulse-fast { animation: pulse 1.5s infinite; }
        [x-cloak] { display: none !important; }
        .header-safe {
            padding-left: calc(2.5rem + env(safe-area-inset-left, 0px));
            padding-right: calc(2.5rem + env(safe-area-inset-right, 0px));
        }
    </style>
</div>

@push('scripts')
<script>
function scoreboardLocal() {
    return {
        // Persisted state
        nameA: 'Tim 1',
        nameB: 'Tim 2',
        gamesToWin: 2,
        detail: [{ t1: 0, t2: 0 }],   // array of {t1, t2}
        // Derived UI
        scores: [0, 0],
        gamesWon: [0, 0],
        currentGame: 0,
        gameLabel: 'Game 1',
        matchOver: false,
        matchWinner: null,
        previousGames: [],
        showSwitchCourt: false,
        courtFlipped: false,
        // Interaction
        timer: null,
        timers: { 1: null, 2: null },
        isLongPress: false,
        activeTouchId: null,      // lock multi-touch: hanya 1 jari yg dihitung
        cooldown: false,
        isFullscreen: false,

        STORAGE_KEY: 'skorcast.public.scoreboard.v1',

        init() {
            this.load();
            this.recompute();

            let update = () => {
                this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
            };
            document.addEventListener('fullscreenchange', update);
            document.addEventListener('webkitfullscreenchange', update);
            document.addEventListener('mozfullscreenchange', update);
            document.addEventListener('MSFullscreenChange', update);
        },

        load() {
            try {
                const raw = localStorage.getItem(this.STORAGE_KEY);
                if (raw) {
                    const d = JSON.parse(raw);
                    this.nameA = d.nameA ?? 'Tim 1';
                    this.nameB = d.nameB ?? 'Tim 2';
                    this.gamesToWin = d.gamesToWin ?? 2;
                    this.detail = Array.isArray(d.detail) && d.detail.length ? d.detail : [{ t1: 0, t2: 0 }];
                }
            } catch (e) {
                // corrupted storage — start fresh
                this.detail = [{ t1: 0, t2: 0 }];
            }
        },

        save() {
            try {
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify({
                    nameA: this.nameA,
                    nameB: this.nameB,
                    gamesToWin: this.gamesToWin,
                    detail: this.detail,
                }));
            } catch (e) { /* storage unavailable — in-memory only */ }
        },

        // ---- Scoring rules (mirrors PublicMatch model) ----
        gameWinner(t1, t2) {
            if (t1 >= 30 && t2 < 30) return 1;
            if (t2 >= 30 && t1 < 30) return 2;
            if (t1 >= 21 && t1 - t2 >= 2) return 1;
            if (t2 >= 21 && t2 - t1 >= 2) return 2;
            return null;
        },

        recalcGamesWon() {
            let w1 = 0, w2 = 0;
            for (const g of this.detail) {
                const w = this.gameWinner(g.t1, g.t2);
                if (w === 1) w1++;
                if (w === 2) w2++;
            }
            return [w1, w2];
        },

        recompute() {
            this.currentGame = 0;
            for (let i = 0; i < this.detail.length; i++) {
                if (this.gameWinner(this.detail[i].t1, this.detail[i].t2) === null) {
                    this.currentGame = i;
                    break;
                }
                if (i === this.detail.length - 1) this.currentGame = i;
            }

            const cur = this.detail[this.currentGame] ?? { t1: 0, t2: 0 };
            this.scores = [cur.t1, cur.t2];

            [this.gamesWon[0], this.gamesWon[1]] = this.recalcGamesWon();

            this.gameLabel = 'Game ' + (this.currentGame + 1);
            this.courtFlipped = this.currentGame % 2 === 1;

            this.matchWinner = null;
            if (this.gamesWon[0] >= this.gamesToWin) this.matchWinner = 1;
            else if (this.gamesWon[1] >= this.gamesToWin) this.matchWinner = 2;
            this.matchOver = this.matchWinner !== null;

            this.previousGames = [];
            for (let i = 0; i < this.detail.length; i++) {
                const g = this.detail[i];
                this.previousGames.push({
                    game: i + 1,
                    winner: this.gameWinner(g.t1, g.t2),
                    score1: g.t1,
                    score2: g.t2,
                });
            }

            if (!this.matchOver && !this.showSwitchCourt) {
                const lastIdx = this.detail.length - 1;
                if (lastIdx >= 0 && this.currentGame === lastIdx) {
                    const g = this.detail[lastIdx];
                    if (this.gameWinner(g.t1, g.t2) !== null) this.showSwitchCourt = true;
                }
            }
        },

        // ---- Actions ----
        clickTeam(team, e) {
            if (e && e.touches && e.touches.length > 1) return; // tolak multi-touch
            if (this.isLongPress) return;
            if (this.cooldown) return;
            if (this.matchOver || this.showSwitchCourt) return;
            this.cooldown = true;
            this.increment(team);
            setTimeout(() => { this.cooldown = false; }, 400);
        },

        startLongPress(team, e) {
            if (this.cooldown) return;
            // Blokir jika sudah ada jari/side lain aktif (multi-touch)
            if (this.activeTouchId !== null) return;
            if (e && e.touches && e.touches.length > 1) return;
            if (e && e.touches && e.touches.length === 1) this.activeTouchId = e.touches[0].identifier;
            else this.activeTouchId = 'mouse-' + team;

            this.isLongPress = false;
            this.timers[team] = setTimeout(() => {
                this.isLongPress = true;
                this.decrement(team);
            }, 600);
        },

        endLongPress(e) {
            // Jika event touch tapi masih ada jari lain menempel, jangan reset dulu
            if (e && e.touches && e.touches.length > 0) return;
            if (this.timers[1]) { clearTimeout(this.timers[1]); this.timers[1] = null; }
            if (this.timers[2]) { clearTimeout(this.timers[2]); this.timers[2] = null; }
            this.activeTouchId = null;
            setTimeout(() => { this.isLongPress = false; }, 50);
        },

        increment(side) {
            if (this.matchOver || this.showSwitchCourt) return;
            const idx = this.currentGame;
            if (!this.detail[idx]) return;
            const key = side === 1 ? 't1' : 't2';
            this.detail[idx][key]++;

            const g = this.detail[idx];
            const gw = this.gameWinner(g.t1, g.t2);

            if (gw !== null) {
                const [w1, w2] = this.recalcGamesWon();
                if (w1 >= this.gamesToWin) {
                    this.matchWinner = 1; this.matchOver = true;
                } else if (w2 >= this.gamesToWin) {
                    this.matchWinner = 2; this.matchOver = true;
                } else {
                    this.showSwitchCourt = true;
                }
            }
            this.recompute();
            this.save();
        },

        decrement(side) {
            if (this.matchOver || this.showSwitchCourt) return;
            const idx = this.currentGame;
            if (!this.detail[idx]) return;
            const key = side === 1 ? 't1' : 't2';
            if (this.detail[idx][key] > 0) this.detail[idx][key]--;
            this.recompute();
            this.save();
        },

        confirmSwitchCourt() {
            this.detail.push({ t1: 0, t2: 0 });
            this.showSwitchCourt = false;
            this.recompute();
            this.save();
        },

        setGamesToWin(n) {
            this.gamesToWin = n;
            this.recompute();
            this.save();
        },

        resetBoard() {
            this.nameA = 'Tim 1';
            this.nameB = 'Tim 2';
            this.gamesToWin = 2;
            this.detail = [{ t1: 0, t2: 0 }];
            this.showSwitchCourt = false;
            this.recompute();
            this.save();
        },

        // ---- Derived getters for template ----
        get leftName()  { return this.courtFlipped ? this.nameB : this.nameA; },
        get rightName() { return this.courtFlipped ? this.nameA : this.nameB; },
        get leftScore()  { return this.courtFlipped ? (this.scores[1] ?? 0) : (this.scores[0] ?? 0); },
        get rightScore() { return this.courtFlipped ? (this.scores[0] ?? 0) : (this.scores[1] ?? 0); },
        get leftSide()  { return this.courtFlipped ? 2 : 1; },
        get rightSide() { return this.courtFlipped ? 1 : 2; },
        get leftIsWinner()  { return this.courtFlipped ? (this.matchWinner === 2) : (this.matchWinner === 1); },
        get rightIsWinner() { return this.courtFlipped ? (this.matchWinner === 1) : (this.matchWinner === 2); },
        get leftGamesWon()  { return this.courtFlipped ? (this.gamesWon[1] ?? 0) : (this.gamesWon[0] ?? 0); },
        get rightGamesWon() { return this.courtFlipped ? (this.gamesWon[0] ?? 0) : (this.gamesWon[1] ?? 0); },
        get clickable() { return !this.matchOver && !this.showSwitchCourt; },

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
                        p.then(() => { if (screen.orientation && screen.orientation.lock) screen.orientation.lock('landscape').catch(() => {}); }).catch(() => {});
                    }
                }
            }
        },
    };
}
</script>
@endpush
