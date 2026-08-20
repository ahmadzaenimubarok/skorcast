<?php

namespace Database\Seeders;

use App\Models\GameMatch;
use App\Models\Participant;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DummyTournamentSeeder extends Seeder
{
    // Kode unik agar seeder idempoten — aman dijalankan ulang.
    private const CODE = 'DEMO25';

    public function run(): void
    {
        $tournament = Tournament::where('code', self::CODE)->first();

        if ($tournament) {
            // Sudah ada → bersihkan match & relasinya lalu rebuild.
            $tournament->gameMatches()->delete();
            $tournament->teams()->each(fn ($t) => $t->members()->detach());
            $tournament->teams()->delete();
            $tournament->participants()->delete();
        } else {
            $tournament = Tournament::create([
                'name' => 'Kejuaraan Bulu Tangkis Terbuka 2025',
                'code' => self::CODE,
                'status' => 'ongoing',
                'games_to_win' => 2,
            ]);
        }

        $playerNames = [
            'Agus Pratama', 'Budi Santoso', 'Citra Dewi', 'Dian Permata',
            'Eko Saputra', 'Fitri Handayani', 'Gilang Ramadhan', 'Hesti Utami',
            'Irfan Maulana', 'Joko Susilo', 'Kartika Sari', 'Lukman Hakim',
            'Maya Anggraini', 'Nugroho Wicaksono', 'Putri Ayuningtyas',
            'Rizki Ramadan',
        ];

        $participants = collect();
        foreach ($playerNames as $name) {
            $participants->push(Participant::create([
                'tournament_id' => $tournament->id,
                'name' => $name,
            ]));
        }

        $teams = collect();
        foreach ($participants as $participant) {
            $team = Team::create([
                'tournament_id' => $tournament->id,
                'name' => $participant->name,
            ]);
            $team->members()->attach($participant);
            $teams->push($team);
        }

        $this->createBracket($tournament, $teams);
    }

    private function createBracket(Tournament $tournament, $teams): void
    {
        $teamIds = $teams->pluck('id')->values();
        $teamCount = $teamIds->count();

        $powerOfTwo = 1;
        while ($powerOfTwo < $teamCount) {
            $powerOfTwo *= 2;
        }

        $firstRoundByes = $powerOfTwo - $teamCount;
        $firstRoundMatches = intdiv($teamCount - $firstRoundByes, 2);

        $matches = [];
        $baseTime = Carbon::now()->subHours(2);

        // --- Ronde 1 ---
        for ($i = 0; $i < $firstRoundMatches; $i++) {
            $t1 = $teamIds[$i * 2];
            $t2 = $teamIds[$i * 2 + 1];
            $match = GameMatch::create([
                'tournament_id' => $tournament->id,
                'round' => 1,
                'match_number' => $i + 1,
                'team1_id' => $t1,
                'team2_id' => $t2,
                'status' => 'pending',
            ]);

            // 2 match pertama sudah selesai (ber-data), sisanya pending.
            if ($i < 2) {
                $this->fillCompletedMatch($match, $baseTime->copy()->addMinutes($i * 40));
            }

            $matches[] = $match;
        }

        $lastMatchNum = $firstRoundMatches;

        if ($firstRoundByes > 0) {
            $byeTeamStartIndex = $teamCount - $firstRoundByes;
            for ($i = 0; $i < $firstRoundByes; $i++) {
                $team = $teams[$byeTeamStartIndex + $i];
                $matches[] = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => 1,
                    'match_number' => $lastMatchNum + $i + 1,
                    'team1_id' => $team->id,
                    'status' => 'completed',
                    'winner_team_id' => $team->id,
                    'started_at' => $baseTime->copy(),
                    'finished_at' => $baseTime->copy()->addMinutes(5),
                    'games_detail' => [['t1' => 0, 't2' => 0, 'started_at' => null, 'finished_at' => null]],
                ]);
            }
            $lastMatchNum += $firstRoundByes;
        }

        $teamsInRound = $firstRoundMatches + $firstRoundByes;

        $prevRoundStart = 0;
        $prevRoundCount = $teamsInRound;
        $round = 1;

        while ($teamsInRound > 1) {
            $round++;
            $matchesInRound = intdiv($teamsInRound, 2);

            for ($i = 0; $i < $matchesInRound; $i++) {
                $prevIdx = $prevRoundStart + $i * 2;

                $match = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'match_number' => $i + 1,
                    'status' => 'pending',
                ]);

                // Match ronde 2 pertama: ongoing (bisa diklik Input Skor)
                if ($round === 2 && $i === 0) {
                    $match->update([
                        'status' => 'ongoing',
                        'started_at' => Carbon::now()->subMinutes(3),
                        'games_detail' => [['t1' => 11, 't2' => 9, 'started_at' => Carbon::now()->subMinutes(3)->toDateTimeString(), 'finished_at' => null]],
                        'score1' => 11,
                        'score2' => 9,
                    ]);
                }

                if (isset($matches[$prevIdx])) {
                    $matches[$prevIdx]->update([
                        'next_match_id' => $match->id,
                        'next_slot' => 1,
                    ]);
                }
                if (isset($matches[$prevIdx + 1])) {
                    $matches[$prevIdx + 1]->update([
                        'next_match_id' => $match->id,
                        'next_slot' => 2,
                    ]);
                }

                $matches[] = $match;
            }

            $prevRoundStart = count($matches) - $matchesInRound;
            $teamsInRound = $matchesInRound;
        }
    }

    /**
     * Isi match dengan skor acak realistis (best-of-3, games_to_win=2).
     * Setiap game punya jam mulai & selesai.
     */
    private function fillCompletedMatch(GameMatch $match, Carbon $start): void
    {
        $games = [];
        $cursor = $start->copy();
        $t1Wins = 0;
        $t2Wins = 0;
        $safety = 0;

        while (($t1Wins < 2 && $t2Wins < 2) && $safety < 5) {
            $t1 = rand(12, 21);
            $t2 = rand(12, 21);
            // Pastikan ada pemenang jelas
            if ($t1 === $t2) {
                $t1++;
            }
            if ($t1 > $t2) {
                $t1Wins++;
                $winner = 1;
            } else {
                $t2Wins++;
                $winner = 2;
            }

            $duration = rand(12, 25); // menit
            $gStart = $cursor->copy();
            $gEnd = $cursor->copy()->addMinutes($duration);

            $games[] = [
                't1' => $t1,
                't2' => $t2,
                'started_at' => $gStart->toDateTimeString(),
                'finished_at' => $gEnd->toDateTimeString(),
            ];

            $cursor = $gEnd->copy()->addMinutes(rand(2, 5)); // jeda antar game
            $safety++;
        }

        $match->update([
            'status' => 'completed',
            'score1' => $t1Wins,
            'score2' => $t2Wins,
            'winner_team_id' => $t1Wins > $t2Wins ? $match->team1_id : $match->team2_id,
            'started_at' => $start->copy(),
            'finished_at' => $cursor->copy(),
            'games_detail' => $games,
            'control_session_id' => null,
            'control_heartbeat' => null,
        ]);
    }
}
