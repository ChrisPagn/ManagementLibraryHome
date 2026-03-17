<?php
// app/Models/Item.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_type_id',
        'title',
        'subtitle',
        'description',
        'cover',
        'author',
        'publisher',
        'published_year',
        'language',
        'isbn',
        'extra',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'extra'          => 'array',  // JSON -> array PHP automatiquement
            'published_year' => 'integer',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByType($query, string $slug)
    {
        return $query->whereHas('type', fn($q) => $q->where('slug', $slug));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isBorrowed(): bool
    {
        return $this->status === 'borrowed';
    }

    // ─── Relations ───────────────────────────────────────────────────────────
    public function type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // Prêt actif (1 seul à la fois)
    public function activeLoan(): HasMany
    {
        return $this->hasMany(Loan::class)
                    ->whereNull('returned_at')
                    ->latest();
    }

   /** 
    * Un item peut appartenir à plusieurs collections, avec un numéro de volume spécifique à chaque collection.
        * La table pivot 'collection_item' contient une colonne 'volume_number' pour stocker ce numéro de volume.
        * Exemple : "Harry Potter à l'école des sorciers" peut être dans la collection "Harry Potter" avec volume_number = 1,
        * et aussi dans la collection "Best Sellers" avec volume_number = null ou 1 selon le cas. 
   */ 
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)
                    ->withPivot('volume_number');
    }
}