<?php
// app/Models/Wishlist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'profile_id',
        'item_type_id',
        'title',
        'author',
        'isbn',
        'note',
        'priority',
        'estimated_price',
        'is_acquired',
        'acquired_at',
    ];

    protected function casts(): array
    {
        return [
            'is_acquired'     => 'boolean',
            'acquired_at'     => 'date',
            'estimated_price' => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    // ─── Helpers ─────────────────────────────────────────────
    public function priorityLabel(): string
    {
        return match($this->priority) {
            'high'   => '🔴 Haute',
            'medium' => '🟡 Moyenne',
            'low'    => '🟢 Basse',
            default  => '—',
        };
    }

    public function markAsAcquired(): void
    {
        $this->update([
            'is_acquired' => true,
            'acquired_at' => now(),
        ]);
    }
}