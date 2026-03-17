<?php
// app/Models/Loan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'item_id',
        'profile_id',
        'loaned_at',
        'due_at',
        'returned_at',
        'borrower_name',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'loaned_at'    => 'date',
            'due_at'       => 'date',
            'returned_at'  => 'date',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNull('returned_at')
                     ->whereNotNull('due_at')
                     ->where('due_at', '<', now());
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    public function isOverdue(): bool
    {
        return ! $this->isReturned()
            && $this->due_at !== null
            && $this->due_at->isPast();
    }

    public function markAsReturned(): void
    {
        $this->update(['returned_at' => now()]);
    }

    // ─── Relations ───────────────────────────────────────────────────────────
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
