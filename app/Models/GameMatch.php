<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    protected $table = 'game_matches';

    protected $fillable = [
        'tournament_id', 'round', 'match_number',
        'team1_id', 'team2_id',
        'score1', 'score2',
        'games_detail',
        'winner_team_id',
        'next_match_id', 'next_slot',
        'status', 'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'round' => 'integer',
            'match_number' => 'integer',
            'score1' => 'integer',
            'score2' => 'integer',
            'games_detail' => 'array',
            'status' => 'string',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'control_heartbeat' => 'datetime',
        ];
    }

    /**
     * Waktu (detik) tanpa heartbeat sebelum lock otomatis lepas.
     * Device keluar halaman → heartbeat berhenti → lepas otomatis setelah ini.
     */
    public const CONTROL_LOCK_TIMEOUT = 30;

    /**
     * Apakah match ini sedang dikunci oleh device lain (bukan session saat ini)?
     */
    public function isControlLockedByOther(?string $sessionId): bool
    {
        if (blank($this->control_session_id)) return false;
        if (blank($sessionId)) return true; // belum punya session → dianggap device lain
        if ($this->control_session_id === $sessionId) return false;

        // Punya session tapi lock sudah expired → bebas
        return ! $this->isControlExpired();
    }

    /**
     * Apakah lock sudah kedaluwarsa (lewat timeout)?
     */
    public function isControlExpired(): bool
    {
        if (blank($this->control_heartbeat)) return true;
        return $this->control_heartbeat->copy()
            ->addSeconds(self::CONTROL_LOCK_TIMEOUT)
            ->isPast();
    }

    /**
     * Klaim / perbarui lock untuk session ini.
     * Mengembalikan true kalau berhasil (bebas atau sudah milik session ini),
     * false kalau dipegang device lain yang masih aktif.
     */
    public function acquireControl(string $sessionId): bool
    {
        // Sudah dibatalkan / milik session sendiri → perbarui heartbeat
        if (blank($this->control_session_id) || $this->control_session_id === $sessionId) {
            $this->control_session_id = $sessionId;
            $this->control_heartbeat = now();
            $this->save();
            return true;
        }

        // Dipegang device lain → cek expiry
        if ($this->isControlExpired()) {
            $this->control_session_id = $sessionId;
            $this->control_heartbeat = now();
            $this->save();
            return true;
        }

        return false; // masih dipegang device lain yang aktif
    }

    /**
     * Lepas lock (jika milik session ini, atau force-release oleh admin).
     */
    public function releaseControl(?string $sessionId = null, bool $force = false): void
    {
        if ($force || blank($sessionId) || $this->control_session_id === $sessionId) {
            $this->control_session_id = null;
            $this->control_heartbeat = null;
            $this->save();
        }
    }

    /**
     * Initialize or get games_detail for a new match.
     */
    public function initGames(): void
    {
        $this->games_detail = [[
            't1' => 0,
            't2' => 0,
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
        ]];
        $this->score1 = 0;
        $this->score2 = 0;
        $this->save();
    }

    /**
     * Get the current game index (0-based).
     */
    public function currentGameIndex(): int
    {
        if (!$this->games_detail) return 0;
        // Find the first game that hasn't been won yet
        foreach ($this->games_detail as $i => $game) {
            if ($this->gameWinner($game['t1'], $game['t2']) === null) {
                return $i;
            }
        }
        // All games are done
        return count($this->games_detail) - 1;
    }

    /**
     * Get current game scores.
     */
    public function currentScores(): array
    {
        $detail = $this->games_detail ?? [['t1' => 0, 't2' => 0]];
        $idx = $this->currentGameIndex();
        $game = $detail[$idx] ?? ['t1' => 0, 't2' => 0];
        return [$game['t1'], $game['t2']];
    }

    /**
     * Total games won by each team.
     */
    public function gamesWon(): array
    {
        if (!$this->games_detail) return [0, 0];
        $w1 = 0; $w2 = 0;
        foreach ($this->games_detail as $i => $game) {
            $winner = $this->gameWinner($game['t1'], $game['t2']);
            if ($winner === 1) $w1++;
            if ($winner === 2) $w2++;
        }
        return [$w1, $w2];
    }

    /**
     * Check if a game is over and who won.
     * Returns 1 (team1), 2 (team2), or null (not over).
     */
    public function gameWinner(int $t1, int $t2): ?int
    {
        // Cap at 30: first to 30 wins
        if ($t1 >= 30 && $t2 < 30) return 1;
        if ($t2 >= 30 && $t1 < 30) return 2;

        // Normal win by 2, minimum 21
        if ($t1 >= 21 && $t1 - $t2 >= 2) return 1;
        if ($t2 >= 21 && $t2 - $t1 >= 2) return 2;

        return null;
    }

    /**
     * Check if the match is over and who won.
     * Returns 1 (team1), 2 (team2), or null.
     */
    public function matchWinner(?int $gamesToWin = null): ?int
    {
        [$w1, $w2] = $this->gamesWon();
        $target = $gamesToWin ?? 2;
        if ($w1 >= $target) return 1;
        if ($w2 >= $target) return 2;
        return null;
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'next_match_id');
    }

    public function isTeam1Winner(): bool
    {
        return $this->winner_team_id && $this->winner_team_id === $this->team1_id;
    }

    public function isTeam2Winner(): bool
    {
        return $this->winner_team_id && $this->winner_team_id === $this->team2_id;
    }

    public function scoreDisplay(): string
    {
        if ($this->status === 'pending') {
            return '-';
        }
        return ($this->score1 ?? 0) . ' - ' . ($this->score2 ?? 0);
    }

    public function winnerName(): ?string
    {
        return $this->winner?->name;
    }

    /**
     * Apakah match ini adalah bye?
     * Bye terjadi saat jumlah tim ganjil — satu tim otomatis maju tanpa bertanding.
     */
    public function isBye(): bool
    {
        if ($this->status !== 'completed') return false;
        return is_null($this->team1_id) || is_null($this->team2_id);
    }

    /**
     * Waktu mulai game ke-i (Carbon|null). Disimpan sebagai string di games_detail.
     */
    public function gameStartedAt(int $index): ?\Carbon\Carbon
    {
        $game = $this->games_detail[$index] ?? null;
        if (!$game || empty($game['started_at'])) return null;
        return \Carbon\Carbon::parse($game['started_at']);
    }

    /**
     * Waktu selesai game ke-i (Carbon|null).
     */
    public function gameFinishedAt(int $index): ?\Carbon\Carbon
    {
        $game = $this->games_detail[$index] ?? null;
        if (!$game || empty($game['finished_at'])) return null;
        return \Carbon\Carbon::parse($game['finished_at']);
    }

    /**
     * Durasi game ke-i dalam format "Xm Ys" (string|null).
     * Null kalau game belum selesai atau timestamp kosong.
     */
    public function gameDuration(int $index): ?string
    {
        $start = $this->gameStartedAt($index);
        $end = $this->gameFinishedAt($index);
        if (!$start || !$end) return null;
        $secs = $start->diffInSeconds($end);
        $m = intdiv($secs, 60);
        $s = $secs % 60;
        return $m > 0 ? "{$m}m {$s}s" : "{$s}s";
    }
}
