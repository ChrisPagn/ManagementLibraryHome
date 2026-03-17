<?php
// app/Filament/Widgets/StatsOverviewWidget.php

namespace App\Filament\Widgets;

use App\Models\Collection;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Profile;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalItems     = Item::count();
        $availableItems = Item::where('status', 'available')->count();
        $borrowedItems  = Item::where('status', 'borrowed')->count();
        $activeLoans    = Loan::whereNull('returned_at')->count();
        $overdueLoans   = Loan::whereNull('returned_at')
                              ->whereNotNull('due_at')
                              ->where('due_at', '<', now())
                              ->count();
        $collections    = Collection::count();
        $profiles       = Profile::count();

        return [
            Stat::make('Total médiathèque', $totalItems)
                ->description("{$availableItems} disponibles · {$borrowedItems} empruntés")
                ->descriptionIcon('heroicon-o-book-open')
                ->color('primary')
                ->chart(
                    Item::selectRaw('COUNT(*) as count')
                        ->groupBy('created_at')
                        ->orderBy('created_at')
                        ->limit(7)
                        ->pluck('count')
                        ->toArray()
                ),

            Stat::make('Prêts en cours', $activeLoans)
                ->description(
                    $overdueLoans > 0
                        ? "{$overdueLoans} en retard ⚠️"
                        : 'Aucun retard ✅'
                )
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color($overdueLoans > 0 ? 'danger' : 'success'),

            Stat::make('Collections', $collections)
                ->description('Séries suivies')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('warning'),

            Stat::make('Profils familiaux', $profiles)
                ->description('Membres actifs')
                ->descriptionIcon('heroicon-o-users')
                ->color('gray'),
        ];
    }
}