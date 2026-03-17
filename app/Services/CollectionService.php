<?php
// app/Services/CollectionService.php

namespace App\Services;

use App\Models\Collection;
use App\Importers\OpenLibraryImporter;
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

    /**
     * Détecte les items existants en bibliothèque
     * qui correspondent aux tomes d'une collection.
     * Retourne les matches pour confirmation.
     */
    public function findExistingItemsForSeries(string $seriesName): array
    {
        $importer = app(OpenLibraryImporter::class);
        $volumes  = $importer->fetchSeriesVolumes($seriesName);

        $matches = [];

        foreach ($volumes as $volume) {
            // Cherche par ISBN d'abord (certitude absolue)
            $item = null;

            if (! empty($volume['isbn'])) {
                $item = Item::where('isbn', $volume['isbn'])->first();
            }

            // Sinon par titre similaire
            if (! $item && ! empty($volume['title'])) {
                $item = Item::where('title', 'like', '%' . $volume['title'] . '%')
                            ->first();
            }

            $matches[] = [
                'volume_data'  => $volume,
                'existing_item' => $item,   // null si pas trouvé en bibliothèque
                'in_library'   => $item !== null,
            ];
        }

        return $matches;
    }

    /**
     * Rattache automatiquement les items trouvés à une collection.
     * $confirmedItemIds = IDs des items que l'utilisateur a confirmés.
     */
    public function attachConfirmedItems(
        Collection $collection,
        array $confirmedItemIds
    ): int {
        $count = 0;

        foreach ($confirmedItemIds as $itemId => $volumeNumber) {
            $item = Item::find($itemId);
            if ($item) {
                $this->attachItem($collection, $item, $volumeNumber ?: null);
                $count++;
            }
        }

        return $count;
    }

}