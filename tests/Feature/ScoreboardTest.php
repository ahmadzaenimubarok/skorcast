<?php

namespace Tests\Feature;

use App\Livewire\Scoreboard;
use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ScoreboardTest extends TestCase
{
    use RefreshDatabase;

    private function ongoingMatch(int $gamesToWin = 2): GameMatch
    {
        $tournament = Tournament::create([
            'name' => 'Test Match',
            'status' => 'ongoing',
            'games_to_win' => $gamesToWin,
            'code' => 'T' . uniqid(),
        ]);

        $t1 = Team::create(['name' => 'Tim 1', 'tournament_id' => $tournament->id]);
        $t2 = Team::create(['name' => 'Tim 2', 'tournament_id' => $tournament->id]);

        $match = GameMatch::create([
            'tournament_id' => $tournament->id,
            'round' => 1,
            'match_number' => 1,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'status' => 'ongoing',
        ]);
        $match->initGames();

        return $match;
    }

    public function test_increment_increases_current_game_score(): void
    {
        $match = $this->ongoingMatch();

        Livewire::test(Scoreboard::class, ['gameMatch' => $match])
            ->call('increment', 1)
            ->call('increment', 1)
            ->assertHasNoErrors();

        $match->refresh();
        $this->assertEquals(2, $match->games_detail[0]['t1']);
        $this->assertEquals(0, $match->games_detail[0]['t2']);
    }

    public function test_decrement_does_not_go_below_zero(): void
    {
        $match = $this->ongoingMatch();

        Livewire::test(Scoreboard::class, ['gameMatch' => $match])
            ->call('decrement', 1) // already 0 -> stays 0
            ->assertHasNoErrors();

        $match->refresh();
        $this->assertEquals(0, $match->games_detail[0]['t1']);
    }

    public function test_game_won_at_21_with_margin_2(): void
    {
        $match = $this->ongoingMatch();

        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $match]);
        for ($i = 0; $i < 21; $i++) {
            $component->call('increment', 1);
        }
        $component->assertHasNoErrors();

        $match->refresh();
        // 21-0 -> team1 menang game 1, match belum selesai (best of 3)
        $this->assertEquals(1, $match->gamesWon()[0]);
        $this->assertEquals('ongoing', $match->status);
    }

    public function test_game_requires_margin_of_2(): void
    {
        $match = $this->ongoingMatch();
        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $match]);

        // 20-20 (belum ada yang menang)
        for ($i = 0; $i < 20; $i++) $component->call('increment', 1);
        for ($i = 0; $i < 20; $i++) $component->call('increment', 2);

        $match->refresh();
        $this->assertNull($match->gameWinner($match->games_detail[0]['t1'], $match->games_detail[0]['t2']));

        // 21-20 -> masih belum menang (butuh margin 2)
        $component->call('increment', 1);
        $match->refresh();
        $this->assertNull($match->gameWinner($match->games_detail[0]['t1'], $match->games_detail[0]['t2']));
        $this->assertEquals([21, 20], [$match->games_detail[0]['t1'], $match->games_detail[0]['t2']]);

        // 22-20 -> win
        $component->call('increment', 1);
        $match->refresh();
        $this->assertEquals(1, $match->gameWinner($match->games_detail[0]['t1'], $match->games_detail[0]['t2']));
    }

    public function test_cap_at_30_wins(): void
    {
        $match = $this->ongoingMatch();
        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $match]);

        for ($i = 0; $i < 30; $i++) $component->call('increment', 1);
        for ($i = 0; $i < 29; $i++) $component->call('increment', 2); // 30-29

        $match->refresh();
        $this->assertEquals(1, $match->gameWinner($match->games_detail[0]['t1'], $match->games_detail[0]['t2']));
    }

    public function test_best_of_3_match_completes_and_advances_winner(): void
    {
        $tournament = Tournament::create([
            'name' => 'BO3',
            'status' => 'ongoing',
            'games_to_win' => 2,
            'code' => 'BO3' . uniqid(),
        ]);
        $t1 = Team::create(['name' => 'Tim 1', 'tournament_id' => $tournament->id]);
        $t2 = Team::create(['name' => 'Tim 2', 'tournament_id' => $tournament->id]);
        $t3 = Team::create(['name' => 'Tim 3', 'tournament_id' => $tournament->id]);

        $final = GameMatch::create([
            'tournament_id' => $tournament->id,
            'round' => 2,
            'match_number' => 1,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'status' => 'ongoing',
        ]);
        $final->initGames();

        // Next match (team3 already placed)
        $next = GameMatch::create([
            'tournament_id' => $tournament->id,
            'round' => 3,
            'match_number' => 1,
            'team1_id' => $t3->id,
            'team2_id' => null,
            'status' => 'pending',
        ]);
        $final->next_match_id = $next->id;
        $final->next_slot = 2;
        $final->save();

        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $final]);

        // Game 1: t1 21-0
        for ($i = 0; $i < 21; $i++) $component->call('increment', 1);
        $component->call('confirmSwitchCourt');
        // Game 2: t1 21-0 -> match over (2-0)
        for ($i = 0; $i < 21; $i++) $component->call('increment', 1);

        $final->refresh();
        $this->assertEquals('completed', $final->status);
        $this->assertEquals($t1->id, $final->winner_team_id);

        // Winner advanced to next match slot 2 (status tetap pending sampai admin mulai)
        $next->refresh();
        $this->assertEquals($t1->id, $next->team2_id);
        $this->assertEquals('pending', $next->status);
    }

    public function test_cannot_increment_when_locked_by_other_device(): void
    {
        $match = $this->ongoingMatch();

        // Lock sudah dipegang device lain (session berbeda, heartbeat masih aktif)
        // -> saat mount, component mendeteksi lockedByOther = true.
        $match->control_session_id = 'other-device';
        $match->control_heartbeat = now();
        $match->save();

        Livewire::test(Scoreboard::class, ['gameMatch' => $match])
            ->call('increment', 1)
            ->assertHasNoErrors();

        $match->refresh();
        $this->assertEquals(0, $match->games_detail[0]['t1'], 'Locked-by-other harus memblokir increment');
    }

    public function test_final_match_completion_marks_tournament_completed(): void
    {
        $tournament = Tournament::create([
            'name' => 'Final',
            'status' => 'ongoing',
            'games_to_win' => 1,
            'code' => 'FIN' . uniqid(),
        ]);
        $t1 = Team::create(['name' => 'Tim 1', 'tournament_id' => $tournament->id]);
        $t2 = Team::create(['name' => 'Tim 2', 'tournament_id' => $tournament->id]);

        $match = GameMatch::create([
            'tournament_id' => $tournament->id,
            'round' => 1,
            'match_number' => 1,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'status' => 'ongoing',
        ]);
        $match->initGames();

        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $match]);
        for ($i = 0; $i < 21; $i++) $component->call('increment', 1);

        $tournament->refresh();
        $this->assertEquals('completed', $tournament->status);
    }

    public function test_init_games_sets_game0_started_at(): void
    {
        $match = $this->ongoingMatch();
        $match->refresh();

        $this->assertNotNull($match->games_detail[0]['started_at'] ?? null, 'game 0 harus punya started_at');
        $this->assertNull($match->games_detail[0]['finished_at'] ?? null, 'game 0 belum selesai');
    }

    public function test_finishing_game_sets_finished_at(): void
    {
        $match = $this->ongoingMatch(); // best of 3
        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $match]);
        for ($i = 0; $i < 21; $i++) $component->call('increment', 1); // 21-0 -> game 1 over

        $match->refresh();
        $this->assertNotNull($match->games_detail[0]['finished_at'] ?? null, 'game 0 harus punya finished_at setelah menang');
    }

    public function test_confirm_switch_court_sets_started_at_for_new_game(): void
    {
        $match = $this->ongoingMatch();
        $component = Livewire::test(Scoreboard::class, ['gameMatch' => $match]);
        for ($i = 0; $i < 21; $i++) $component->call('increment', 1);
        $component->call('confirmSwitchCourt'); // game 2 mulai

        $match->refresh();
        $this->assertCount(2, $match->games_detail);
        $this->assertNotNull($match->games_detail[1]['started_at'] ?? null, 'game 1 (index 1) harus punya started_at');
    }
}
