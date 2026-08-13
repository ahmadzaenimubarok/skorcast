<?php

namespace App\Livewire;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.register')]
class RegistrationPage extends Component
{
    public Tournament $tournament;
    public string $code;

    public string $name = '';
    public ?int $group = null;

    public function mount(string $code)
    {
        $this->code = $code;
        $this->tournament = Tournament::where('code', $code)
            ->with('participants')
            ->firstOrFail();
    }

    public function register()
    {
        if ($this->tournament->status !== 'draft') {
            session()->flash('error', 'Pendaftaran sudah ditutup.');
            return;
        }

        // Kuota total maksimal peserta
        $max = $this->tournament->max_participants;
        if ($max && $this->tournament->participants()->count() >= $max) {
            session()->flash('error', "Maaf, kuota sudah penuh (maksimal {$max} peserta).");
            return;
        }

        $this->name = trim($this->name);
        $this->validate(['name' => 'required|string|max:255']);

        $exists = $this->tournament->participants()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($this->name)])
            ->exists();

        if ($exists) {
            session()->flash('error', 'Nama "' . $this->name . '" sudah terdaftar.');
            return;
        }

        $groupName = null;
        if ($this->tournament->use_groups) {
            $options = $this->tournament->groupOptions();
            if (! $this->group || ! isset($options[$this->group])) {
                session()->flash('error', 'Pilih kelompok terlebih dahulu.');
                return;
            }
            $groupName = $options[$this->group];

            // Kuota per kelompok (rata)
            $capacity = $this->tournament->groupCapacity();
            if ($capacity) {
                $groupCount = $this->tournament->participants()->where('group_name', $groupName)->count();
                if ($groupCount >= $capacity) {
                    session()->flash('error', "{$groupName} sudah penuh (maksimal {$capacity} peserta).");
                    return;
                }
            }
        }

        $this->tournament->participants()->create([
            'name' => $this->name,
            'group_name' => $groupName,
        ]);

        $this->name = '';
        $this->group = null;
        $this->tournament->load('participants');
        session()->flash('message', 'Pendaftaran berhasil! Nama kamu sudah masuk daftar.');
    }

    // Dipanggil via wire:poll agar daftar + jumlah peserta sinkron di semua device
    public function refresh()
    {
        $this->tournament = Tournament::where('code', $this->code)
            ->with('participants')
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.registration-page');
    }
}
