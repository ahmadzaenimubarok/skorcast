<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Scoreboard publik mandiri (/s).
 *
 * Seluruh state skor disimpan di localStorage masing-masing browser —
 * tidak ada DB bersama, sehingga 2 orang/perangkat berbeda yang membuka
 * halaman ini TIDAK saling terkait. Semua logika scoring ada di sisi
 * klien (Alpine, lihat livewire/public-scoreboard.blade.php).
 *
 * Komponen ini hanya merender tampilan statis (tanpa polling/wire).
 */
#[Layout('layouts.scoreboard')]
class PublicScoreboard extends Component
{
    public function render()
    {
        return view('livewire.public-scoreboard');
    }
}
