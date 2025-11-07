<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AuthLoginLivewire;
use App\Livewire\AuthRegisterLivewire;
use App\Livewire\FinanceIndex;
use App\Livewire\FinanceForm;

Route::get('/', fn() => redirect()->route('auth.login'));

// Login & Register
Route::get('/auth/login', AuthLoginLivewire::class)->name('auth.login');
Route::get('/auth/register', AuthRegisterLivewire::class)->name('auth.register');

// Logout
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect()->route('auth.login');
})->name('logout');

// Protected routes
Route::middleware(['auth'])->prefix('app')->group(function () {
    // 📊 Halaman daftar catatan keuangan
    Route::get('/finances', FinanceIndex::class)->name('app.finances.index');

    // ➕ Tambah data keuangan
    Route::get('/finances/create', FinanceForm::class)->name('app.finances.create');

    // ✏️ Edit / ubah data keuangan
    Route::get('/finances/{id}/edit', FinanceForm::class)->name('app.finances.edit');
});
