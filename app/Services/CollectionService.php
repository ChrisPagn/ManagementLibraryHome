<?php
// app/Services/CollectionService.php

namespace App\Services;

use App\Models\Collection;
use App\Models\Item;

class CollectionService
{
    /**
     * Attache un item à une collection avec son numéro de tome.
     */
    public function attachItem(Collection $collection, Item $item, ?int $volumeNumber = null): void
    {
        $collection->items()->syncWithoutDetaching([
            $item->id => ['volume_number' => $volumeNumber],
        ]);
    }

    /**
     * Détache un item d'une collection.
     */
    public function detachItem(Collection $collection, Item $item): void
    {
        $collection->items()->detach($item->id);
    }

    /**
     * Retourne un résumé complet de l'état d'une collection.
     */
    public function getCollectionStatus(Collection $collection): array
    {
        $collection->load('items');

        $owned   = $collection->ownedVolumeNumbers();
        $missing = $collection->missingVolumeNumbers();

        return [
            'name'                => $collection->name,
            'total_volumes'       => $collection->total_volumes,
            'owned_count'         => count($owned),
            'missing_count'       => count($missing),
            'owned_volumes'       => $owned,
            'missing_volumes'     => $missing,
            'completion_percent'  => $collection->completionPercentage(),
            'is_complete'         => $collection->is_complete,
        ];
    }

    /**
     * Retourne toutes les collections avec leurs éléments manquants.
     */
    public function getAllWithMissing(): \Illuminate\Database\Eloquent\Collection
    {
        return Collection::with(['items', 'type'])
                         ->whereNotNull('total_volumes')
                         ->get()
                         ->filter(fn($c) => count($c->missingVolumeNumbers()) > 0);
    }
}