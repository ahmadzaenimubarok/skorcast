<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicTournamentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_lists_tournaments_grouped_by_status(): void
    {
        $draft = Tournament::create(['name' => 'Piala Baru', 'status' => Tournament::STATUS_DRAFT]);
        $ongoing = Tournament::create(['name' => 'Liga Berjalan', 'status' => Tournament::STATUS_ONGOING]);
        $completed = Tournament::create(['name' => 'Piala Lama', 'status' => Tournament::STATUS_COMPLETED]);
        Tournament::create(['name' => 'Arsip Rahasia', 'status' => Tournament::STATUS_ARCHIVED]);

        Livewire::test(\App\Livewire\PublicTournaments::class)
            ->assertSee('Piala Baru')
            ->assertSee('Liga Berjalan')
            ->assertSee('Piala Lama')
            ->assertDontSee('Arsip Rahasia')
            ->assertDontSee('Draft'); // label publik bukan 'draft' lagi
    }

    public function test_draft_links_to_registration_page(): void
    {
        $draft = Tournament::create(['name' => 'Piala Baru', 'status' => Tournament::STATUS_DRAFT]);

        Livewire::test(\App\Livewire\PublicTournaments::class)
            ->assertSeeHtml('href="/r/' . $draft->code . '"');
    }

    public function test_ongoing_and_completed_link_to_public_bracket(): void
    {
        $ongoing = Tournament::create(['name' => 'Liga', 'status' => Tournament::STATUS_ONGOING]);
        $completed = Tournament::create(['name' => 'Piala', 'status' => Tournament::STATUS_COMPLETED]);

        Livewire::test(\App\Livewire\PublicTournaments::class)
            ->assertSeeHtml('href="/t/' . $ongoing->code . '"')
            ->assertSeeHtml('href="/t/' . $completed->code . '"');
    }

    public function test_route_returns_200(): void
    {
        $this->get('/turnamen')->assertOk();
    }
}
