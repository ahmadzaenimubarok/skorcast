<?php

namespace Tests\Feature;

use App\Livewire\TournamentShow;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** Helper: buat turnamen dengan kelompok aktif (3 kelompok). */
    private function tournamentWithGroups(): Tournament
    {
        return Tournament::create([
            'name' => 'Grup Test',
            'use_groups' => true,
            'group_count' => 3,
            'group_names' => ['Putra', 'Putri', 'Campuran'],
        ]);
    }

    /** Helper: isi peserta 4 Putra, 2 Putri, 2 Campuran. */
    private function addEightParticipants(Tournament $t): void
    {
        $rows = [
            ['A1', 'Putra'], ['A2', 'Putra'], ['A3', 'Putra'], ['A4', 'Putra'],
            ['B1', 'Putri'], ['B2', 'Putri'],
            ['C1', 'Campuran'], ['C2', 'Campuran'],
        ];
        foreach ($rows as [$name, $group]) {
            $t->participants()->create(['name' => $name, 'group_name' => $group]);
        }
    }

    public function test_generate_by_group_pairs_two_per_group(): void
    {
        $t = $this->tournamentWithGroups();
        $this->addEightParticipants($t);

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams', 'byGroup')
            ->assertHasNoErrors();

        $t->load('teams.members');
        // Putra 4 -> 2 tim, Putri 2 -> 1 tim, Campuran 2 -> 1 tim = 4 tim
        $this->assertEquals(4, $t->teams()->count());

        $names = $t->teams->pluck('name')->sort()->values()->all();
        $this->assertEquals(['Campuran 1', 'Putra 1', 'Putra 2', 'Putri 1'], $names);

        // Setiap tim harus berisi 2 orang
        foreach ($t->teams as $team) {
            $this->assertEquals(2, $team->members->count(), "Tim {$team->name} harus 2 orang");
            $this->assertEquals($team->group_name, $team->members->first()->group_name, "Tim {$team->name} harus berisi peserta {$team->group_name}");
        }

        $this->assertEquals(8, $t->teams->sum(fn ($team) => $team->members->count()), 'Semua peserta harus masuk tim');
    }

    public function test_generate_by_group_leaves_odd_one_out_as_solo(): void
    {
        $t = Tournament::create([
            'name' => 'Tim Besar',
            'use_groups' => true,
            'group_count' => 2,
            'group_names' => ['Putra', 'Putri'],
        ]);

        foreach (['A1', 'A2', 'A3', 'A4', 'A5'] as $n) {
            $t->participants()->create(['name' => $n, 'group_name' => 'Putra']);
        }
        $t->participants()->create(['name' => 'B1', 'group_name' => 'Putri']);

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams', 'byGroup')
            ->assertHasNoErrors();

        $t->load('teams.members');
        // Putra 5 -> 3 tim (2+2+1), Putri 1 -> 1 tim = 4 tim
        $this->assertEquals(4, $t->teams()->count());

        $putraTeams = $t->teams->where('group_name', 'Putra');
        $this->assertEquals(3, $putraTeams->count());
        $this->assertEquals([1, 2, 2], $putraTeams->map->members->map->count()->sort()->values()->all());

        $this->assertEquals(6, $t->teams->sum(fn ($team) => $team->members->count()), 'Semua peserta harus masuk tim');
    }

    public function test_generate_random_avoids_same_group_pairing_when_groups_active(): void
    {
        $t = $this->tournamentWithGroups();
        $this->addEightParticipants($t);

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams', 'random')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(4, $t->teams()->count(), '8 peserta -> 4 tim');

        foreach ($t->teams as $team) {
            $groups = $team->members->pluck('group_name');
            $this->assertNotEquals(
                $groups[0] ?? null,
                $groups[1] ?? null,
                "Tim {$team->name} berisi dua peserta sekelompok: {$groups->join(' & ')}"
            );
        }
    }

    public function test_generate_random_without_groups_matches_old_behavior(): void
    {
        $t = Tournament::create(['name' => 'Tanpa Kelompok', 'use_groups' => false]);

        foreach (['A', 'B', 'C', 'D', 'E'] as $n) {
            $t->participants()->create(['name' => $n, 'group_name' => null]);
        }

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams', 'random')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(3, $t->teams()->count(), '5 peserta -> 2 tim berpasangan + 1 tim solo');
        $this->assertEquals(5, $t->teams->sum(fn ($team) => $team->members->count()));
    }

    public function test_manual_pairing_creates_team_from_same_group(): void
    {
        $t = $this->tournamentWithGroups();
        $this->addEightParticipants($t);

        $a1 = $t->participants()->where('name', 'A1')->first();
        $a2 = $t->participants()->where('name', 'A2')->first();

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('pairingClick', $a1->id)
            ->call('pairingClick', $a2->id)
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(1, $t->teams()->count(), '1 tim terbentuk dari pasangan manual');

        $team = $t->teams->first();
        $this->assertEquals('Putra 1', $team->name);
        $this->assertEquals(['A1', 'A2'], $team->members->pluck('name')->sort()->values()->all());
        $this->assertEquals('Putra', $team->group_name);
    }

    public function test_manual_pairing_rejects_different_groups(): void
    {
        $t = $this->tournamentWithGroups();
        $this->addEightParticipants($t);

        $a1 = $t->participants()->where('name', 'A1')->first();
        $b1 = $t->participants()->where('name', 'B1')->first();

        $component = Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('pairingClick', $a1->id)
            ->call('pairingClick', $b1->id);

        $component->assertHasNoErrors();
        $this->assertNull($component->get('pairingParticipantId'), 'Pilihan direset setelah gagal');

        $this->assertEquals(0, $t->teams()->count(), 'Tidak ada tim terbentuk');
    }

    public function test_unpair_team_returns_members_to_pool(): void
    {
        $t = $this->tournamentWithGroups();
        $this->addEightParticipants($t);

        $a1 = $t->participants()->where('name', 'A1')->first();
        $a2 = $t->participants()->where('name', 'A2')->first();

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('pairingClick', $a1->id)
            ->call('pairingClick', $a2->id);

        $t->refresh();
        $this->assertEquals(1, $t->teams()->count(), 'Tim terbentuk dari pasangan manual');

        $teamId = $t->teams()->first()->id;

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('unpairTeam', $teamId);

        $t->refresh();
        $this->assertEquals(0, $t->teams()->count(), 'Tim dibongkar');
    }

    public function test_singles_mode_creates_one_team_per_participant(): void
    {
        $t = Tournament::create([
            'name' => 'Turnamen Tunggal',
            'play_mode' => Tournament::PLAY_MODE_SINGLES,
            'use_groups' => false,
        ]);

        foreach (['Andi', 'Budi', 'Cici', 'Dedi', 'Eka'] as $n) {
            $t->participants()->create(['name' => $n, 'group_name' => null]);
        }

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams', 'random')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(5, $t->teams()->count(), '5 pemain -> 5 tim (1 pemain = 1 tim)');

        $names = $t->teams->pluck('name')->sort()->values()->all();
        $this->assertEquals(['Andi', 'Budi', 'Cici', 'Dedi', 'Eka'], $names, 'Nama tim = nama pemain');

        // Setiap tim berisi tepat 1 anggota
        foreach ($t->teams as $team) {
            $this->assertEquals(1, $team->members->count(), "Tim {$team->name} harus 1 orang");
        }
    }

    public function test_singles_mode_by_group_keeps_group_name(): void
    {
        $t = Tournament::create([
            'name' => 'Tunggal Berkelompok',
            'play_mode' => Tournament::PLAY_MODE_SINGLES,
            'use_groups' => true,
            'group_count' => 2,
            'group_names' => ['Putra', 'Putri'],
        ]);

        $t->participants()->create(['name' => 'Andi', 'group_name' => 'Putra']);
        $t->participants()->create(['name' => 'Budi', 'group_name' => 'Putra']);
        $t->participants()->create(['name' => 'Cici', 'group_name' => 'Putri']);

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams', 'byGroup')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(3, $t->teams()->count(), '3 pemain -> 3 tim');

        $putra = $t->teams->where('group_name', 'Putra');
        $putri = $t->teams->where('group_name', 'Putri');
        $this->assertEquals(2, $putra->count());
        $this->assertEquals(1, $putri->count());
        $this->assertEquals(['Andi', 'Budi'], $putra->pluck('name')->sort()->values()->all());
    }

    public function test_set_play_mode_updates_tournament(): void
    {
        $t = Tournament::create(['name' => 'Ubah Mode']);

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('setPlayMode', Tournament::PLAY_MODE_SINGLES)
            ->assertHasNoErrors();

        $this->assertEquals(Tournament::PLAY_MODE_SINGLES, $t->refresh()->play_mode);

        // Set ke mode tidak valid harus diabaikan
        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('setPlayMode', 'triple')
            ->assertHasNoErrors();
        $this->assertEquals(Tournament::PLAY_MODE_SINGLES, $t->refresh()->play_mode);
    }

    public function test_generate_teams_again_clears_old_bracket(): void
    {
        $t = $this->tournamentWithGroups();
        $this->addEightParticipants($t);

        $component = Livewire::test(TournamentShow::class, ['tournament' => $t]);

        // Generate tim -> bracket
        $component->call('generateTeams', 'byGroup')->assertHasNoErrors();
        $t->refresh();
        $this->assertEquals(4, $t->teams()->count());

        $component->call('generateBracket')->assertHasNoErrors();
        $t->refresh();
        $this->assertGreaterThan(0, $t->gameMatches()->count(), 'Bracket harus ada');

        // Generate tim LAGI -> bracket lama harus ikut terhapus,
        // bukan menyisakan match yang menunjuk team_id yang sudah hilang.
        $component->call('generateTeams', 'byGroup')->assertHasNoErrors();
        $t->refresh();
        $this->assertEquals(4, $t->teams()->count(), 'Tim baru terbentuk');
        $this->assertEquals(0, $t->gameMatches()->count(), 'Match bracket lama harus bersih');
    }
}
