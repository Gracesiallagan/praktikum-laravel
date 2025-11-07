<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Database\QueryException;

class AuthRegisterLivewire extends Component
{
    public $name, $email, $password, $password_confirmation;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:6|same:password_confirmation',
        'password_confirmation' => 'required|string|min:6',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min' => 'Kata sandi minimal 6 karakter.',
        'password.same' => 'Kata sandi dan konfirmasi harus sama.',
    ];

    public function updated($property) { $this->validateOnly($property); }

    public function register()
    {
        $this->validate();

        try {
            User::create([
                'name' => trim($this->name),
                'email' => strtolower(trim($this->email)),
                'password' => Hash::make($this->password),
            ]);
        } catch (QueryException $ex) {
            \Log::error('Register failed: '.$ex->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'icon'=>'error','title'=>'Gagal Mendaftar','text'=>'Terjadi kesalahan server.'
            ]);
            return;
        }

        $this->reset(['name','email','password','password_confirmation']);
        session()->flash('success', 'Pendaftaran berhasil! Silakan login.');
        return redirect()->to('/auth/login');
    }

    public function render()
    {
        return view('livewire.auth-register-livewire')->layout('layouts.auth');
    }
}
