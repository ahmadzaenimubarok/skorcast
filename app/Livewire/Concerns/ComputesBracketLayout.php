<?php

namespace App\Livewire\Concerns;

/**
 * Hitung layout bracket dengan connector (SVG lines).
 *
 * Dipakai oleh PublicBracket & TournamentShow agar logika posisi konsisten.
 *
 * Setiap match diposisikan absolut berdasarkan pusat feeder-nya:
 * - Round 1: compact vertikal (i * unit + H/2)
 * - Round berikutnya: pusat = rata-rata pusat 2 feeder (match 2i & 2i+1 di round
 *   sebelumnya). Jika salah satu feeder void (null vs null), pusat = feeder yang ada.
 *
 * Void match (slot yang tidak akan pernah terisi) disembunyikan — tidak dirender,
 * tidak digambar. Match pending di round > 1 yang belum ada timnya TETAP tampil
 * (itu slot sah yang akan terisi).
 *
 * Garis connector digambar per pasangan round dari tepi KANAN kolom round ini
 * ke tepi KIRI kolom round berikutnya.
 */
trait ComputesBracketLayout
{
    /**
     * @param \Illuminate\Support\Collection $matches gameMatches ordered by round, match_number
     * @return array
     */
    private function bracketLayout($matches, int $cardH = 184, int $cardW = 224, int $vGap = 16, int $hGap = 64, int $headerH = 40): array
    {
        $byRound = [];
        foreach ($matches as $m) {
            $byRound[$m->round][] = $m;
        }
        ksort($byRound);
        $roundNums = array_keys($byRound);

        $empty = ['rounds' => [], 'roundLeft' => [], 'tops' => [], 'lines' => [], 'width' => 0, 'height' => 0, 'cardH' => $cardH, 'headerH' => $headerH];
        if (empty($roundNums)) {
            return $empty;
        }

        $firstRound = $roundNums[0];
        $roundLeft = [];
        $centers = [];
        $tops = [];
        $maxCenter = 0;
        $matchStatus = [];

        // Void match = slot yang tidak akan pernah terisi:
        // - Round 1: kedua slot kosong
        // - Round > 1: kedua feeder-nya void (recursive)
        $void = [];
        foreach ($roundNums as $idx => $r) {
            $void[$r] = [];
            $prev = $idx > 0 ? $roundNums[$idx - 1] : null;
            foreach ($byRound[$r] as $i => $m) {
                if ($prev === null) {
                    $void[$r][$i] = is_null($m->team1_id) && is_null($m->team2_id);
                } else {
                    $v1 = $void[$prev][2 * $i] ?? true;
                    $v2 = $void[$prev][2 * $i + 1] ?? true;
                    $void[$r][$i] = $v1 && $v2;
                }
            }
        }

        foreach ($roundNums as $idx => $r) {
            $roundLeft[$r] = ($r - $firstRound) * ($cardW + $hGap);
            $centers[$r] = [];
            $prev = $idx > 0 ? $roundNums[$idx - 1] : null;

            $compact = 0;
            foreach ($byRound[$r] as $i => $m) {
                if ($void[$r][$i]) {
                    continue; // void match — hidden
                }

                if ($prev === null) {
                    $c = $compact * ($cardH + $vGap) + $cardH / 2;
                } else {
                    $f1 = $centers[$prev][2 * $i] ?? null;
                    $f2 = $centers[$prev][2 * $i + 1] ?? null;
                    if ($f1 !== null && $f2 !== null) {
                        $c = ($f1 + $f2) / 2;
                    } elseif ($f1 !== null) {
                        $c = $f1;
                    } elseif ($f2 !== null) {
                        $c = $f2;
                    } else {
                        $c = $compact * ($cardH + $vGap) + $cardH / 2;
                    }
                }

                $centers[$r][$i] = $c;
                $tops[$m->id] = $headerH + $c - $cardH / 2;
                $matchStatus[$r][$i] = $m->status;
                $maxCenter = max($maxCenter, $c);
                $compact++;
            }
        }

        // Connector lines (y dihitung relatif ke area card, ditambah headerH di akhir)
        $lines = [];
        foreach ($roundNums as $idx => $r) {
            if ($idx === count($roundNums) - 1) {
                break; // round terakhir tidak punya next
            }
            $next = $roundNums[$idx + 1];
            // Garis membentang dari tepi KANAN kolom round ini ke tepi KIRI kolom round next
            $x1 = $roundLeft[$r] + $cardW;
            $gapCenter = $x1 + $hGap / 2;
            $x2 = $x1 + $hGap;

            foreach ($byRound[$next] as $j => $m) {
                $yc = $centers[$next][$j] ?? null;
                if ($yc === null) {
                    continue;
                }
                $f1 = $centers[$r][2 * $j] ?? null;
                $f2 = $centers[$r][2 * $j + 1] ?? null;

                // Relasi "terpakai" = feeder match sudah selesai (pemenang sudah di-advance
                // ke slot next match). Garis hijau menandai jalur yang sudah dilalui pemenang.
                $used1 = ($matchStatus[$r][2 * $j] ?? null) === 'completed';
                $used2 = ($matchStatus[$r][2 * $j + 1] ?? null) === 'completed';

                if ($f1 !== null && $f2 !== null) {
                    // bracket penuh: dua feeder bertemu di tengah gap
                    $lines[] = [$x1, $f1, $gapCenter, $f1, $used1];
                    $lines[] = [$x1, $f2, $gapCenter, $f2, $used2];
                    $lines[] = [$gapCenter, min($f1, $f2), $gapCenter, max($f1, $f2), $used1 || $used2];
                    $lines[] = [$gapCenter, $yc, $x2, $yc, $used1 || $used2];
                } elseif ($f1 !== null) {
                    // bye: garis lurus dari feeder ke match berikutnya
                    $lines[] = [$x1, $f1, $x2, $f1, $used1];
                } elseif ($f2 !== null) {
                    $lines[] = [$x1, $f2, $x2, $f2, $used2];
                }
            }
        }

        foreach ($lines as &$ln) {
            $ln[1] += $headerH;
            $ln[3] += $headerH;
        }
        unset($ln);

        $width = (count($roundNums) - 1) * ($cardW + $hGap) + $cardW;
        $height = $headerH + $maxCenter + $cardH / 2;

        // Hanya round yang punya match non-void
        $visibleRounds = [];
        foreach ($roundNums as $r) {
            $vis = [];
            foreach ($byRound[$r] as $i => $m) {
                if (!$void[$r][$i]) {
                    $vis[] = $m;
                }
            }
            if (count($vis) > 0) {
                $visibleRounds[$r] = $vis;
            }
        }

        // Nama babak: dihitung dari posisi terhadap Final (akhir).
        // 3 ronde → Perempat Final, Semifinal, Final. 4 ronde → 16 Besar, Perempat Final, Semifinal, Final.
        $stageNames = ['Final', 'Semifinal', 'Perempat Final', '16 Besar', '32 Besar', '64 Besar', '128 Besar'];
        $roundNames = [];
        $visibleKeys = array_keys($visibleRounds);
        $visibleTotal = count($visibleKeys);
        foreach ($visibleKeys as $idx => $r) {
            $fromEnd = $visibleTotal - 1 - $idx;
            $roundNames[$r] = $stageNames[$fromEnd] ?? ('Ronde ' . ($idx + 1));
        }

        return [
            'rounds' => $visibleRounds,
            'roundNames' => $roundNames,
            'roundLeft' => $roundLeft,
            'tops' => $tops,
            'lines' => $lines,
            'width' => (int) round($width),
            'height' => (int) round($height),
            'cardH' => $cardH,
            'headerH' => $headerH,
        ];
    }
}
