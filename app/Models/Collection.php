<?php
// app/Models/Collection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Collection extends Model
{
    protected $fillable = [
        'item_type_id',
        'name',
        'slug',
        'description',
        'author',
        'total_volumes',
        'is_complete',
        'cover',
    ];

    protected function casts(): array
    {
        return [
            'is_complete'    => 'boolean',
            'total_volumes'  => 'integer',
        ];
    }

    // ─── Boot ────────────────────────────────────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Collection $collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->name);
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────────────────
    public function type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
                    ->withPivot('volume_number')
                    ->orderByPivot('volume_number');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Retourne les numéros de tomes possédés.
     */
    public function ownedVolumeNumbers(): array
    {
        return $this->items
                    ->pluck('pivot.volume_number')
                    ->filter()
                    ->sort()
                    ->values()
                    ->toArray();
    }

    /**
     * Retourne les numéros de tomes manquants.
     * Nécessite que total_volumes soit défini.
     */
    public function missingVolumeNumbers(): array
    {
        if (! $this->total_volumes) {
            return [];
        }

        $owned    = $this->ownedVolumeNumbers();
        $expected = range(1, $this->total_volumes);

        return array_values(array_diff($expected, $owned));
    }

    /**
     * Pourcentage de complétion de la collection.
     */
    public function completionPercentage(): int
    {
        if (! $this->total_volumes || $this->total_volumes === 0) {
            return 0;
        }

        $owned = count($this->ownedVolumeNumbers());
        return (int) round(($owned / $this->total_volumes) * 100);
    }
}