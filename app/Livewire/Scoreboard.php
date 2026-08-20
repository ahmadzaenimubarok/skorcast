<?php

namespace App\Livewire;

use App\Models\GameMatch;
use Livewire\Component;

class Scoreboard extends Component
{
    public GameMatch $match;
    public array $scores = [0, 0];
    public array $gamesWon = [0, 0];
    public int $currentGame = 0;
    public string $gameLabel = 'Game 1';
    public bool $matchOver = false;
    public ?int $matchWinner = null;
    public array $previousGames = [];
    public bool $readonly = false;
    public bool $showSwitchCourt = false;
    public bool $courtFlipped = false;
    public string $tournamentCode = '';
    public string $tournamentName = '';
    public int $gamesToWin = 2;

    // --- Device control lock (admin only) ---
    public bool $lockedByOther = false;   // true kalau scoreboard ini dikunci device lain
    public bool $controlActive = false;   // true kalau device ini pemegang lock
    public ?string $lockOwnerLabel = null; // untuk pesan peringatan
    private ?string $sessionId = null;
    public bool $isWasit = false;   // true kalau operator adalah wasit (bukan admin)

    /**
     * Lifecycle: setiap request Livewire (klik/poll) me-rehydrate komponen.
     * $sessionId bersifat private → TIDAK ikut tersimpan di snapshot, sehingga
     * harus di-set ulang di sini. Tanpa ini, sessionId jadi null setelah request
     * kedua dan refreshState() salah menganggap scoreboard dikunci device lain.
     */
    public function hydrate(): void
    {
        if (!$this->readonly) {
            $this->sessionId = session()->getId();
        }
    }

    public function mount(GameMatch $gameMatch)
    {
        $this->match = $gameMatch->load(['team1.members', 'team2.members', 'tournament']);

        // Simpan tournament info sebagai properti terpisah (reliable di Livewire)
        $this->tournamentCode = $this->match->tournament?->code ?? '';
        $this->tournamentName = $this->match->tournament?->name ?? 'Badminton Fun Match';
        $this->gamesToWin = $this->match->tournament?->games_to_win ?? 2;

        // Public scoreboard route → read-only (lock TIDAK berlaku di publik)
        if (request()->route()->named('public.scoreboard')) {
            $this->readonly = true;
        }

        // Deteksi sesi wasit → navigasi kembali ke panel wasit (bukan admin)
        $this->isWasit = auth()->check() && auth()->user()->role === 'wasit';

        if (!$this->match->games_detail) {
            $this->match->initGames();
        }

        // Lock hanya untuk kontrol admin (route scoreboard.show, bukan publik)
        if (!$this->readonly) {
            $this->sessionId = session()->getId();
            $this->initControlLock();
        }

        $this->refreshState();
    }

    /**
     * Cek / klaim lock saat mount. Set flag lockedByOther kalau dipegang device lain.
     */
    private function initControlLock(): void
    {
        if ($this->match->isControlLockedByOther($this->sessionId)) {
            $this->lockedByOther = true;
            $this->controlActive = false;
        } else {
            $this->match->acquireControl($this->sessionId);
            $this->lockedByOther = false;
            $this->controlActive = true;
        }
    }

    /**
     * Dipanggil lewat Alpine Poll tiap 5 detik: perbarui heartbeat + cek apakah
     * lock masih milik device ini. Kalau admin melepas paksa → redirect ke detail turnamen.
     */
    public function heartbeat(): void
    {
        if ($this->readonly) return;

        // Baca state DB terbaru (snapshot bisa stale setelah admin force-release)
        $this->match->refresh();

        // Lock masih milik device ini → perbarui heartbeat, tetap kontrol
        if ($this->match->control_session_id === $this->sessionId) {
            $this->match->control_heartbeat = now();
            $this->match->save();
            $this->lockedByOther = false;
            $this->controlActive = true;
            return;
        }

        // Lock sudah dilepas/dialihkan → balik ke bracket sebelumnya
        // (wasit → panel wasit, bukan admin)
        if ($this->isWasit) {
            $this->redirect($this->tournamentCode ? '/wasit/' . $this->tournamentCode : '/wasit');
        } else {
            $this->redirect('/admin/tournaments/' . $this->match->tournament_id);
        }
    }

    public function refreshState(): void
    {
        $this->match->refresh();

        // Sinkron status lock (dipanggil tiap poll $refresh):
        // kalau device ini pemegang → pastikan masih aktif; kalau bukan → kunci.
        if (!$this->readonly) {
            if ($this->match->control_session_id === $this->sessionId) {
                $this->lockedByOther = false;
                $this->controlActive = true;
            } else {
                $this->lockedByOther = true;
                $this->controlActive = false;
            }
        }

        $this->currentGame = $this->match->currentGameIndex();
        [$s1, $s2] = $this->match->currentScores();
        $this->scores = [$s1, $s2];
        [$w1, $w2] = $this->match->gamesWon();
        $this->gamesWon = [$w1, $w2];
        $this->gameLabel = 'Game ' . ($this->currentGame + 1);
        $this->courtFlipped = $this->currentGame % 2 == 1;
        $this->matchWinner = $this->match->matchWinner($this->gamesToWin);
        $this->matchOver = $this->matchWinner !== null;

        // Auto-detect: jika game terakhir sudah selesai tapi match belum over,
        // tampilkan prompt pindah lapangan (cover case refresh mid-switch)
        if (!$this->matchOver && !$this->showSwitchCourt) {
            $detail = $this->match->games_detail ?? [];
            $lastIdx = count($detail) - 1;
            if ($lastIdx >= 0 && $this->currentGame === $lastIdx) {
                $lastGame = $detail[$lastIdx];
                if ($this->match->gameWinner($lastGame['t1'], $lastGame['t2']) !== null) {
                    $this->showSwitchCourt = true;
                }
            }
        }

        $detail = $this->match->games_detail ?? [];
        $this->previousGames = [];
        foreach ($detail as $i => $game) {
            $winner = $this->match->gameWinner($game['t1'], $game['t2']);
            $this->previousGames[] = [
                'game' => $i + 1,
                'winner' => $winner,
                'score1' => $game['t1'],
                'score2' => $game['t2'],
            ];
        }
    }

    /**
     * Click on team area = increment score.
     */
    public function increment(int $team): void
    {
        if ($this->readonly) return;
        if ($this->lockedByOther) return;          // dikunci device lain → blokir
        if ($this->match->status !== 'ongoing') return;
        if ($this->matchOver) return;
        if ($this->showSwitchCourt) return;

        $detail = $this->match->games_detail;
        $idx = $this->currentGame;

        if (!isset($detail[$idx])) return;

        $detail[$idx][$team === 1 ? 't1' : 't2']++;

        // Check if game is over
        $t1 = $detail[$idx]['t1'];
        $t2 = $detail[$idx]['t2'];
        $gameWinner = $this->match->gameWinner($t1, $t2);

        if ($gameWinner !== null) {
            // Game over — catat waktu selesai game ini
            $detail[$idx]['finished_at'] = now()->toDateTimeString();

            // Game over — check if match is also over
            [$w1, $w2] = $this->recalculateGamesWon($detail);
            $this->match->score1 = $w1;
            $this->match->score2 = $w2;
            $this->match->games_detail = $detail;

            $matchWinner = null;
            if ($w1 >= $this->gamesToWin) $matchWinner = 1;
            elseif ($w2 >= $this->gamesToWin) $matchWinner = 2;

            if ($matchWinner !== null) {
                // Match over!
                $winnerId = $matchWinner === 1 ? $this->match->team1_id : $this->match->team2_id;
                $this->match->winner_team_id = $winnerId;
                $this->match->status = 'completed';
                $this->match->finished_at = now();
                $this->match->save();

                // Advance winner to next match
                $this->advanceWinner($winnerId);
            } else {
                // Game over — prompt court switch before starting next game
                $this->match->save();
                $this->showSwitchCourt = true;
            }
        } else {
            $this->match->games_detail = $detail;
            $this->match->save();
        }
    }

    /**
     * Long-press on team area = decrement score (min 0).
     */
    public function decrement(int $team): void
    {
        if ($this->readonly) return;
        if ($this->lockedByOther) return;          // dikunci device lain → blokir
        if ($this->match->status !== 'ongoing') return;
        if ($this->matchOver) return;
        if ($this->showSwitchCourt) return;

        $detail = $this->match->games_detail;
        $idx = $this->currentGame;

        if (!isset($detail[$idx])) return;

        $key = $team === 1 ? 't1' : 't2';
        if ($detail[$idx][$key] > 0) {
            $detail[$idx][$key]--;
        }

        $this->match->games_detail = $detail;
        $this->match->save();
    }

    /**
     * Close the scoreboard — return to bracket.
     */
    public function close()
    {
        // Reload tournament karena relationship tidak diserialisasi Livewire
        $this->match->load('tournament');
        $code = $this->match->tournament?->code;
        $tournamentId = $this->match->tournament_id;

        // Operator keluar scoreboard → lepas lock agar device lain bisa mengambil alih
        if (!$this->readonly && $this->sessionId) {
            $this->match->releaseControl($this->sessionId);
        }

        if ($this->readonly && $code) {
            $this->redirect('/t/' . $code);
        } elseif ($this->isWasit) {
            $this->redirect($this->tournamentCode ? '/wasit/' . $this->tournamentCode : '/wasit');
        } else {
            $this->redirect('/admin/tournaments/' . $tournamentId);
        }
    }

    private function recalculateGamesWon(array $detail): array
    {
        $w1 = 0; $w2 = 0;
        foreach ($detail as $game) {
            $winner = $this->match->gameWinner($game['t1'], $game['t2']);
            if ($winner === 1) $w1++;
            if ($winner === 2) $w2++;
        }
        return [$w1, $w2];
    }

    private function advanceWinner(int $winnerTeamId): void
    {
        if ($this->match->next_match_id) {
            $nextMatch = GameMatch::find($this->match->next_match_id);
            if ($nextMatch) {
                $column = 'team' . $this->match->next_slot . '_id';
                $nextMatch->$column = $winnerTeamId;
                $nextMatch->save();
            }
        } else {
            // Final match selesai
            $this->match->tournament->update(['status' => 'completed']);
        }
    }

    public function confirmSwitchCourt(): void
    {
        if ($this->readonly) return;
        if ($this->lockedByOther) return;          // dikunci device lain → blokir
        $detail = $this->match->games_detail;
        $detail[] = [
            't1' => 0,
            't2' => 0,
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
        ];
        $this->match->games_detail = $detail;
        $this->match->save();
        $this->showSwitchCourt = false;
    }

    public function render()
    {
        $this->refreshState();

        if ($this->readonly) {
            $closeUrl = $this->tournamentCode ? '/t/' . $this->tournamentCode : '';
        } elseif ($this->isWasit) {
            $closeUrl = $this->tournamentCode ? '/wasit/' . $this->tournamentCode : '/wasit';
        } else {
            $closeUrl = '/admin/tournaments/' . $this->match->tournament_id;
        }

        return view('livewire.scoreboard', ['closeUrl' => $closeUrl])
            ->layout('layouts.scoreboard', ['closeUrl' => $closeUrl]);
    }
}
