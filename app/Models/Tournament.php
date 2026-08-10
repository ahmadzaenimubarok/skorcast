<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    // Status machine — sumber kebenaran tunggal untuk nilai status turnamen.
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ONGOING,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = ['name', 'code', 'status', 'original_status', 'games_to_win', 'max_participants', 'use_groups', 'group_count', 'group_names', 'is_public'];

    protected $attributes = [
        'status' => 'draft',
        'games_to_win' => 2,
        'use_groups' => false,
        'is_public' => true,
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'games_to_win' => 'integer',
            'max_participants' => 'integer',
            'use_groups' => 'boolean',
            'group_count' => 'integer',
            'group_names' => 'array',
            'is_public' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tournament $tournament) {
            if (empty($tournament->code)) {
                $tournament->code = strtoupper(Str::random(6));
            }
        });
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    /** Label bahasa Indonesia untuk badge status di UI. */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ONGOING => 'Berjalan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_ARCHIVED => 'Diarsipkan',
            default => ucfirst((string) $this->status),
        };
    }

    /** Kuota peserta per kelompok (dibagi rata, pembulatan ke atas). Null kalau kelompok nonaktif. */
    public function groupCapacity(): ?int
    {
        if (! $this->use_groups || ! $this->group_count || $this->group_count < 1) {
            return null;
        }

        if (! $this->max_participants) {
            return null; // tanpa batas total → tidak ada kuota per kelompok
        }

        return (int) ceil($this->max_participants / $this->group_count);
    }

    /** Daftar nama kelompok: nama custom (bila diisi) atau fallback "Kelompok 1..N". */
    public function groupOptions(): array
    {
        $options = [];
        if ($this->use_groups && $this->group_count) {
            $names = $this->group_names ?? [];
            for ($i = 1; $i <= $this->group_count; $i++) {
                $options[$i] = ! empty($names[$i - 1]) ? $names[$i - 1] : 'Kelompok ' . $i;
            }
        }

        return $options;
    }
}
