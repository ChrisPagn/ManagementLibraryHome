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

    // Un item peut avoir un propriétaire (profil) qui l'a ajouté à la bibliothèque.
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'owner_profile_id');
    }

    /* Un item peut avoir plusieurs avis (reviews) de la part des utilisateurs, avec une note (rating) et un commentaire.
        * La relation est définie avec la classe ItemReview, qui contient les champs 'rating' (note) et 'comment' (commentaire).
        * Exemple : Un utilisateur peut laisser un avis pour "Harry Potter à l'école des sorciers" avec une note de 5 étoiles et un commentaire "Un classique indémodable !".
    */
    public function reviews(): HasMany
    {
        return $this->hasMany(ItemReview::class);
    }

    /* Calcul de la note moyenne d'un item à partir de ses avis (reviews). 
        * La méthode averageRating() utilise la relation reviews() pour calculer la moyenne des notes (rating) des avis associés à cet item.
        * Si aucun avis n'a de note, la méthode retourne null.
        * Exemple : Si "Harry Potter à l'école des sorciers" a 3 avis avec des notes de 5, 4 et 5 étoiles, averageRating() retournera 4.67.
    */
    public function averageRating(): ?float
    {
        return $this->reviews()->whereNotNull('rating')->avg('rating');
    }
        
}