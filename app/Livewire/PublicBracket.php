<?php

namespace App\Livewire;

use App\Livewire\Concerns\ComputesBracketLayout;
use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public', params: ['title' => 'Live Bracket — Skor Cast'])]
class PublicBracket extends Component
{
    use ComputesBracketLayout;

    public Tournament $tournament;
    public string $code;

    public function mount(string $code)
    {
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
        // Reload from DB so polling picks up score changes
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

        return view('livewire.public-bracket', [
            'bracketRounds' => $bracketRounds,
            'bracketLayout' => $this->bracketLayout($this->tournament->gameMatches),
            'champion' => $champion,
        ]);
    }
}
