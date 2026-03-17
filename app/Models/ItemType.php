<?php
// app/Models/ItemType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ItemType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    // ─── Boot : génère le slug automatiquement ───────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ItemType $itemType) {
            if (empty($itemType->slug)) {
                $itemType->slug = Str::slug($itemType->name);
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────────────────
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}