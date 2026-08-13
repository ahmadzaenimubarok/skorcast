<?php

namespace App\Livewire;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing', params: [
    'title' => 'Panel Wasit — Skor Cast',
    'description' => 'Bracket turnamen khusus wasit — lihat dan input skor.',
])]
class WasitDashboard extends Component
{
    public function mount(): void
    {
        // Proteksi: harus login
        if (! auth()->check()) {
            $this->redirect('/login', navigate: true);
            return;
        }
        // Admin tidak boleh di area wasit
        if (auth()->user()->role === 'admin') {
            $this->redirect('/admin', navigate: true);
        }
    }

    public function render()
    {
        $tournaments = Tournament::query()
            ->where('status', '!=', Tournament::STATUS_ARCHIVED)
            ->where('is_public', true)
            ->withCount(['participants', 'teams'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.wasit-dashboard', [
            'ongoing' => $tournaments->where('status', Tournament::STATUS_ONGOING)->values(),
            'draft' => $tournaments->where('status', Tournament::STATUS_DRAFT)->values(),
            'completed' => $tournaments->where('status', Tournament::STATUS_COMPLETED)->values(),
        ]);
    }
}
