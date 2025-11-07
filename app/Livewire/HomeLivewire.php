<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HomeLivewire extends Component
{
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function render()
    {
        return view('livewire.home-livewire');
    }
}
