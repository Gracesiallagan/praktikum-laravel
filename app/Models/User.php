<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nama tabel
    protected $table = 'users';

    // Kolom yang bisa diisi
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Kolom yang disembunyikan (tidak ikut ke JSON)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Mutator: otomatis hash password sebelum disimpan.
     */
    public function setPasswordAttribute($value)
    {
        // Jika belum di-hash, hash sekarang
        if (!Str::startsWith($value, '$2y$')) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Relasi ke tabel keuangan
     */
    public function finances()
    {
        return $this->hasMany(Finance::class);
    }
}
