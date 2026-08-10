<?php

namespace App\Livewire;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing', params: [
    'title' => 'Daftar Turnamen — Skor Cast',
    'description' => 'Lihat turnamen badminton komunitas di Skor Cast — belum mulai, berjalan, dan selesai.',
    'canonical' => 'https://skorcast.online/turnamen',
])]
class PublicTournaments extends Component
{
    public function render()
    {
        $all = Tournament::query()
            ->where('status', '!=', Tournament::STATUS_ARCHIVED)
            ->withCount(['participants', 'teams'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.public-tournaments', [
            'draft' => $all->where('status', Tournament::STATUS_DRAFT)->values(),
            'ongoing' => $all->where('status', Tournament::STATUS_ONGOING)->values(),
            'completed' => $all->where('status', Tournament::STATUS_COMPLETED)->values(),
        ]);
    }
}
