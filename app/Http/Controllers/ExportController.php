<?php
// app/Http/Controllers/ExportController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    /**
     * Export PDF de la médiathèque complète.
     */
    public function mediathequePdf()
    {
        $items = Item::with(['type', 'tags'])
                     ->orderBy('item_type_id')
                     ->orderBy('title')
                     ->get()
                     ->groupBy('type.name');

        $stats = [
            'total'     => Item::count(),
            'available' => Item::where('status', 'available')->count(),
            'borrowed'  => Item::where('status', 'borrowed')->count(),
            'date'      => Carbon::now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('exports.mediatheque-pdf', compact('items', 'stats'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('mediatheque-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export JSON complet (backup admin).
     */
    public function mediathequeJson()
    {
        $data = [
            'exported_at' => now()->toIso8601String(),
            'version'     => '1.0',
            'stats' => [
                'total_items' => Item::count(),
                'total_loans' => Loan::count(),
            ],
            'items' => Item::with(['type', 'tags', 'loans.profile'])
                           ->get()
                           ->map(fn ($item) => [
                               'id'             => $item->id,
                               'title'          => $item->title,
                               'subtitle'       => $item->subtitle,
                               'type'           => $item->type?->name,
                               'author'         => $item->author,
                               'publisher'      => $item->publisher,
                               'published_year' => $item->published_year,
                               'language'       => $item->language,
                               'isbn'           => $item->isbn,
                               'status'         => $item->status,
                               'description'    => $item->description,
                               'tags'           => $item->tags->pluck('name'),
                               'extra'          => $item->extra,
                               'loans'          => $item->loans->map(fn ($loan) => [
                                   'profile'     => $loan->profile?->name,
                                   'loaned_at'   => $loan->loaned_at?->toDateString(),
                                   'due_at'      => $loan->due_at?->toDateString(),
                                   'returned_at' => $loan->returned_at?->toDateString(),
                               ]),
                           ]),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="backup-mediatheque-'
                                     . now()->format('Y-m-d') . '.json"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}