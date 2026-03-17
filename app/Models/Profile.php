<?php
// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'avatar',
        'pin',
        'role',
    ];

    protected $hidden = [
        'pin', // On ne l'expose jamais par défaut
    ];

    protected function casts(): array
    {
        return [
            'role' => 'string',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function checkPin(string $pin): bool
    {
        return $this->pin === $pin;
    }

    // ─── Relations ───────────────────────────────────────────────────────────
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // Prêts en cours (non retournés)
    public function activeLoans(): HasMany
    {
        return $this->hasMany(Loan::class)
                    ->whereNull('returned_at');
    }
}