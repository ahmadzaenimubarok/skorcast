<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Login Admin')]
class AdminLogin extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
        }
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $target = Auth::user()->role === 'wasit' ? '/wasit' : '/admin';
            $this->redirectIntended($target);
            return;
        }

        $this->addError('email', 'Email atau password salah.');
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.admin-login');
    }
}
