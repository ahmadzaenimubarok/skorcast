<?php

use App\Livewire\AdminLogin;
use App\Livewire\PublicBracket;
use App\Livewire\PublicScoreboard;
use App\Livewire\PublicTournaments;
use App\Livewire\RegistrationPage;
use App\Livewire\Scoreboard;
use App\Livewire\TournamentIndex;
use App\Livewire\TournamentShow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::get('/login', AdminLogin::class)->name('login');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', TournamentIndex::class)->name('tournaments.index');
    Route::get('/tournaments/{tournament}', TournamentShow::class)->name('tournaments.show');
});

Route::get('/scoreboard/{gameMatch}', Scoreboard::class)->name('scoreboard.show');
Route::get('/t/{code}/scoreboard/{gameMatch}', Scoreboard::class)->name('public.scoreboard');
Route::get('/t/{code}', PublicBracket::class)->name('public.bracket');
Route::get('/r/{code}', RegistrationPage::class)->name('registration.show');

// Scoreboard publik standalone — tanpa turnamen, tanpa tim, tanpa kode. Langsung main.
Route::get('/s', PublicScoreboard::class)->name('public.scoreboard');

// Daftar turnamen publik — tanpa login: belum mulai, berjalan, selesai.
Route::get('/turnamen', PublicTournaments::class)->name('public.tournaments');
