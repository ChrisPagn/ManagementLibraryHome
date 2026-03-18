<?php

namespace App\Models;
// app/Models/ItemSuggestion.php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSuggestion extends Model
{
     protected $fillable = [
        'profile_id', 'item_type_id', 'title',
        'author', 'isbn', 'note', 'status', 'admin_note',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
