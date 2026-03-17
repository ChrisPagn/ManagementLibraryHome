<?php
// app/Importers/OpenLibraryImporter.php

namespace App\Importers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenLibraryImporter
{
    private const BASE_URL = 'https://openlibrary.org';

    /**
     * Récupère les données d'un livre via son ISBN.
     * Retourne un tableau normalisé ou null si non trouvé.
     */
    public function fetchByIsbn(string $isbn): ?array
    {
        $isbn = $this->cleanIsbn($isbn);

        try {
            // Appel API Open Library
            $response = Http::timeout(10)
                ->get(self::BASE_URL . "/api/books", [
                    'bibkeys'  => "ISBN:{$isbn}",
                    'format'   => 'json',
                    'jscmd'    => 'data',
                ]);

            if (! $response->successful()) {
                Log::warning("OpenLibrary: réponse non-OK pour ISBN {$isbn}");
                return null;
            }

            $body = $response->json();
            $key  = "ISBN:{$isbn}";

            if (empty($body[$key])) {
                Log::info("OpenLibrary: aucun résultat pour ISBN {$isbn}");
                return null;
            }

            return $this->normalize($body[$key]);

        } catch (\Exception $e) {
            Log::error("OpenLibrary: erreur pour ISBN {$isbn} — " . $e->getMessage());
            return null;
        }
    }

    /**
     * Normalise la réponse brute d'Open Library
     * vers un tableau standard pour notre application.
     */
    private function normalize(array $raw): array
    {
        // Auteurs : tableau d'objets [{name: "..."}]
        $authors = collect($raw['authors'] ?? [])
            ->pluck('name')
            ->implode(', ');

        // Éditeurs
        $publishers = collect($raw['publishers'] ?? [])
            ->pluck('name')
            ->first();

        // Année de publication
        $publishedYear = null;
        if (! empty($raw['publish_date'])) {
            preg_match('/\d{4}/', $raw['publish_date'], $matches);
            $publishedYear = $matches[0] ?? null;
        }

        // Couverture
        $coverUrl = $raw['cover']['large']
            ?? $raw['cover']['medium']
            ?? $raw['cover']['small']
            ?? null;

        // Description
        $description = null;
        if (! empty($raw['excerpts'][0]['text'])) {
            $description = $raw['excerpts'][0]['text'];
        }

        return [
            'title'          => $raw['title'] ?? 'Titre inconnu',
            'subtitle'       => $raw['subtitle'] ?? null,
            'description'    => $description,
            'author'         => $authors ?: null,
            'publisher'      => $publishers,
            'published_year' => $publishedYear ? (int) $publishedYear : null,
            'language'       => $this->extractLanguage($raw),
            'cover_url'      => $coverUrl,
        ];
    }

    /**
     * Extrait la langue depuis les données brutes.
     */
    private function extractLanguage(array $raw): ?string
    {
        $lang = $raw['languages'][0]['key'] ?? null;

        if (! $lang) return null;

        // "/languages/fre" → "fr"
        $map = [
            'fre' => 'fr',
            'eng' => 'en',
            'spa' => 'es',
            'deu' => 'de',
            'ita' => 'it',
            'nld' => 'nl',
        ];

        $code = basename($lang); // "fre"
        return $map[$code] ?? $code;
    }

    /**
     * Nettoie un ISBN (supprime tirets et espaces).
     */
    private function cleanIsbn(string $isbn): string
    {
        return preg_replace('/[^0-9X]/', '', strtoupper($isbn));
    }

    /**
     * Recherche une série/collection par son nom.
     */
    public function searchSeries(string $name): array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::BASE_URL . '/search.json', [
                    'q'      => $name,
                    'fields' => 'key,title,author_name,number_of_pages_median,series,edition_count',
                    'limit'  => 10,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $docs = $response->json()['docs'] ?? [];

            // Filtre uniquement les résultats qui ont une série
            return collect($docs)
                ->filter(fn($doc) => ! empty($doc['series']))
                ->map(fn($doc) => [
                    'title'         => $doc['title'] ?? null,
                    'series'        => $doc['series'][0] ?? null,
                    'author'        => $doc['author_name'][0] ?? null,
                    'edition_count' => $doc['edition_count'] ?? null,
                ])
                ->values()
                ->toArray();

        } catch (\Exception $e) {
            Log::error("OpenLibrary searchSeries: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Recherche tous les tomes d'une série par son nom.
     * Retourne les données normalisées de chaque tome.
     */
    public function fetchSeriesVolumes(string $seriesName): array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::BASE_URL . '/search.json', [
                    'q'      => "series:\"{$seriesName}\"",
                    'fields' => 'key,title,author_name,first_publish_year,isbn,cover_i,series',
                    'limit'  => 50,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $docs = $response->json()['docs'] ?? [];

            return collect($docs)
                ->filter(fn($doc) => ! empty($doc['isbn']))
                ->map(fn($doc) => [
                    'title'          => $doc['title'] ?? 'Titre inconnu',
                    'author'         => $doc['author_name'][0] ?? null,
                    'published_year' => $doc['first_publish_year'] ?? null,
                    'isbn'           => $doc['isbn'][0] ?? null,
                    'cover_url'      => isset($doc['cover_i'])
                        ? "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-M.jpg"
                        : null,
                    'series'         => $doc['series'][0] ?? $seriesName,
                ])
                ->values()
                ->toArray();

        } catch (\Exception $e) {
            Log::error("OpenLibrary fetchSeriesVolumes: " . $e->getMessage());
            return [];
        }
    }
}