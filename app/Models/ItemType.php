<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ItemType $itemType) {
            if (empty($itemType->slug)) {
                $itemType->slug = Str::slug($itemType->name);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }
}
