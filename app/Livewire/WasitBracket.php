<?php

namespace App\Livewire;

use App\Livewire\Concerns\ComputesBracketLayout;
use App\Models\GameMatch;
use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public', params: ['title' => 'Bracket Wasit — Skor Cast'])]
class WasitBracket extends Component
{
    use ComputesBracketLayout;

    public Tournament $tournament;
    public string $code;

    /** State modal detail pertandingan yang sudah selesai. */
    public ?array $matchDetail = null;
    public bool $showMatchDetail = false;

    public function mount(string $code)
    {
        // Proteksi: harus login
        if (! auth()->check()) {
            $this->redirect('/login', navigate: true);
            return;
        }
        // Admin tidak boleh di area wasit
        if (auth()->user()->role === 'admin') {
            $this->redirect('/admin', navigate: true);
            return;
        }

        $this->code = $code;
        $this->tournament = Tournament::where('code', $code)
            ->with([
                'gameMatches' => fn ($q) => $q->orderBy('round')->orderBy('match_number'),
                'gameMatches.team1.members',
                'gameMatches.team2.members',
                'gameMatches.winner',
            ])
            ->firstOrFail();
    }

    public function openMatchDetail(int $matchId): void
    {
        $match = GameMatch::with(['team1', 'team2', 'winner'])->find($matchId);
        if (! $match || $match->status !== 'completed') {
            return;
        }

        $games = [];
        foreach ($match->games_detail ?? [] as $i => $g) {
            $start = $match->gameStartedAt($i);
            $end = $match->gameFinishedAt($i);
            $games[] = [
                'index' => $i + 1,
                't1' => $g['t1'] ?? 0,
                't2' => $g['t2'] ?? 0,
                'winner' => $match->gameWinner($g['t1'] ?? 0, $g['t2'] ?? 0),
                'start' => $start ? $start->format('H:i:s') : null,
                'end' => $end ? $end->format('H:i:s') : null,
                'duration' => $match->gameDuration($i),
            ];
        }

        $duration = null;
        if ($match->started_at && $match->finished_at) {
            $secs = $match->started_at->diffInSeconds($match->finished_at);
            $duration = intdiv($secs, 60) . 'm ' . ($secs % 60) . 's';
        }

        $this->matchDetail = [
            'team1' => $match->team1?->name ?? ($match->isBye() ? 'BYE' : '—'),
            'team2' => $match->team2?->name ?? ($match->isBye() ? 'BYE' : '—'),
            'score1' => $match->score1 ?? 0,
            'score2' => $match->score2 ?? 0,
            'winner' => $match->winner?->name,
            'isBye' => $match->isBye(),
            'started' => $match->started_at ? $match->started_at->format('H:i:s') : null,
            'finished' => $match->finished_at ? $match->finished_at->format('H:i:s') : null,
            'duration' => $duration,
            'games' => $games,
        ];
        $this->showMatchDetail = true;
    }

    public function closeMatchDetail(): void
    {
        $this->showMatchDetail = false;
        $this->matchDetail = null;
    }

    /**
     * Wasit memulai pertandingan dari bracket (pending -> ongoing).
     * Sama seperti admin: set started_at + initGames (game 1, skor 0-0).
     */
    public function startMatch(int $matchId): void
    {
        $match = GameMatch::findOrFail($matchId);
        if ($match->status !== 'pending') {
            return;
        }
        if (! $match->team1_id || ! $match->team2_id) {
            session()->flash('error', 'Tidak bisa memulai — tim tidak lengkap.');
            return;
        }

        $match->update(['status' => 'ongoing', 'started_at' => now()]);
        $match->initGames();
        $match->save();
    }

    public function render()
    {
        $this->tournament = $this->tournament->fresh();
        $this->tournament->load([
            'gameMatches' => fn ($q) => $q->orderBy('round')->orderBy('match_number'),
            'gameMatches.team1.members',
            'gameMatches.team2.members',
            'gameMatches.winner',
        ]);

        $bracketRounds = $this->tournament->gameMatches
            ->groupBy('round')
            ->sortKeys();

        $champion = null;
        if ($this->tournament->status === 'completed') {
            $finalMatch = $this->tournament->gameMatches()
                ->whereNull('next_match_id')
                ->where('status', 'completed')
                ->first();
            $champion = $finalMatch?->winner;
        }

        return view('livewire.wasit-bracket', [
            'bracketRounds' => $bracketRounds,
            'bracketLayout' => $this->bracketLayout($this->tournament->gameMatches, cardH: 200, cardW: 256),
            'champion' => $champion,
        ]);
    }
}
