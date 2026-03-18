<?php
// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;


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
            // On NE cast PAS le pin en 'hashed' car c'est un PIN court
            // On gère le hash manuellement
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

     // Vérifie le PIN entré contre le hash stocké
    public function checkPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin);
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

    /**
     * Avis laissés par ce profil
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ItemReview::class);
    }

    /**
     * Récupère l'avis laissé par ce profil pour un item donné, ou null s'il n'en a pas laissé
     */
    public function reviewFor(int $itemId): ?ItemReview
    {
        return $this->reviews()->where('item_id', $itemId)->first();
    }

    // Mutateur — hash automatique à la sauvegarde
    public function setPinAttribute(?string $value): void
    {
        if (filled($value)) {
            $this->attributes['pin'] = Hash::make($value);
        }
    }

   

}