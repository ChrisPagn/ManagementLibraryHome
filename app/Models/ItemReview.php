<?php
// app/Models/ItemReview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemReview extends Model
{
    protected $fillable = [
        'item_id',
        'profile_id',
        'reading_status',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    // ─── Relations ───────────────────────────────────────────
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // ─── Helpers ─────────────────────────────────────────────
    public function starsHtml(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating ? '★' : '☆';
        }
        return $stars;
    }

    public function readingStatusLabel(): string
    {
        return match($this->reading_status) {
            'to_read'     => '📚 À lire',
            'in_progress' => '📖 En cours',
            'completed'   => '✅ Terminé',
            'abandoned'   => '🚫 Abandonné',
            default       => '—',
        };
    }
}