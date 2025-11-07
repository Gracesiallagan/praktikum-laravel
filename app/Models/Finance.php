<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Finance extends Model
{
    use HasFactory;

    protected $table = 'finances';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'type',
        'receipt_path',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
