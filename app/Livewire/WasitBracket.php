<?php

namespace App\Livewire;

use App\Livewire\Concerns\ComputesBracketLayout;
use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public', params: ['title' => 'Bracket Wasit — Skor Cast'])]
class WasitBracket extends Component
{
    use ComputesBracketLayout;

    public Tournament $tournament;
    public string $code;

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
