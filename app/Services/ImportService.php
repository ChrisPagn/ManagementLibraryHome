<?php
// app/Services/ImportService.php

namespace App\Services;

use App\Importers\OpenLibraryImporter;
use App\Models\Item;
use App\Models\ItemType;

class ImportService
{
    public function __construct(
        private readonly OpenLibraryImporter $openLibraryImporter,
        private readonly ItemService $itemService,
    ) {}

    /**
     * Importe un livre depuis Open Library en utilisant son ISBN.
     */
    public function importByIsbn(string $isbn): ?Item
    {
        $data = $this->openLibraryImporter->fetchByIsbn($isbn);

        if (! $data) {
            return null;
        }

        $bookType = ItemType::firstOrCreate(
            ['slug' => 'livre'],
            ['name' => 'Livre', 'icon' => 'heroicon-o-book-open']
        );

        // Télécharge la couverture si disponible
        $coverPath = null;
        if (! empty($data['cover_url'])) {
            $coverPath = $this->downloadCover($data['cover_url'], $isbn);
        }

        $itemData = [
            'item_type_id'   => $bookType->id,
            'title'          => $data['title'],
            'subtitle'       => $data['subtitle'] ?? null,
            'description'    => $data['description'] ?? null,
            'author'         => $data['author'] ?? null,
            'publisher'      => $data['publisher'] ?? null,
            'published_year' => $data['published_year'] ?? null,
            'language'       => $data['language'] ?? null,
            'isbn'           => $isbn,
            'cover'          => $coverPath,
            'status'         => 'available',
        ];

        return $this->itemService->create($itemData);
    }

    /**
     * Télécharge une image distante et la stocke localement.
     */
    private function downloadCover(string $url, string $isbn): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $extension = 'jpg';
            $path      = "covers/isbn_{$isbn}.{$extension}";

            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $response->body());

            return $path;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Impossible de télécharger la couverture : {$url}");
            return null;
        }
    }
}