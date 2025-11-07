<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthLoginLivewire extends Component
{
    public $email;
    public $password;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->addError('email', 'Email atau kata sandi salah.');
            return;
        }

        $this->reset(['email', 'password']);
        return redirect()->route('app.finances.index');
    }

    public function render()
    {
        return view('livewire.auth-login-livewire')->layout('layouts.app');
    }
}
