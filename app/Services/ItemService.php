<?php
// app/Services/ItemService.php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    /**
     * Crée un nouvel item avec ses tags et sa couverture.
     */
    public function create(array $data, ?UploadedFile $cover = null): Item
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        if ($cover) {
            $data['cover'] = $cover->store('covers', 'public');
        }

        $item = Item::create($data);
        $item->tags()->sync($tags);

        return $item;
    }

    /**
     * Met à jour un item existant.
     */
    public function update(Item $item, array $data, ?UploadedFile $cover = null): Item
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        if ($cover) {
            // Supprime l'ancienne couverture si elle existe
            if ($item->cover) {
                Storage::disk('public')->delete($item->cover);
            }
            $data['cover'] = $cover->store('covers', 'public');
        }

        $item->update($data);
        $item->tags()->sync($tags);

        return $item;
    }

    /**
     * Supprime un item (soft delete).
     */
    public function delete(Item $item): void
    {
        if ($item->cover) {
            Storage::disk('public')->delete($item->cover);
        }

        $item->delete();
    }

    /**
     * Détecte les doublons potentiels d'un item.
     * Retourne une collection d'items similaires.
     */
    public function findDuplicates(array $data): \Illuminate\Database\Eloquent\Collection
    {
        $query = Item::query();

        // Doublon par ISBN (certitude absolue)
        if (! empty($data['isbn'])) {
            return $query->where('isbn', $data['isbn'])
                         ->get();
        }

        // Doublon par titre + auteur (probabilité forte)
        if (! empty($data['title']) && ! empty($data['author'])) {
            return $query->where('title', 'like', '%' . $data['title'] . '%')
                         ->where('author', 'like', '%' . $data['author'] . '%')
                         ->get();
        }

        // Doublon par titre seul (probabilité faible)
        if (! empty($data['title'])) {
            return $query->where('title', 'like', '%' . $data['title'] . '%')
                         ->get();
        }

        return collect();
    }

    /**
     * Vérifie s'il existe des doublons et retourne true/false.
     */
    public function hasDuplicates(array $data, ?int $excludeId = null): bool
    {
        $duplicates = $this->findDuplicates($data);

        if ($excludeId) {
            $duplicates = $duplicates->where('id', '!=', $excludeId);
        }

        return $duplicates->isNotEmpty();
    }
}