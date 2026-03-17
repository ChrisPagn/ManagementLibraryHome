<?php
// app/Filament/Widgets/LoansByMonthWidget.php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class LoansByMonthWidget extends ChartWidget
{
    protected static ?string $heading = 'Prêts des 6 derniers mois';
    protected static ?int    $sort    = 3;

    protected function getData(): array
    {
        // Génère les 6 derniers mois
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        $loans = Loan::selectRaw("strftime('%Y-%m', loaned_at) as month, COUNT(*) as count")
                     ->where('loaned_at', '>=', now()->subMonths(6))
                     ->groupBy('month')
                     ->orderBy('month')
                     ->pluck('count', 'month');

        $returned = Loan::selectRaw("strftime('%Y-%m', returned_at) as month, COUNT(*) as count")
                        ->whereNotNull('returned_at')
                        ->where('returned_at', '>=', now()->subMonths(6))
                        ->groupBy('month')
                        ->orderBy('month')
                        ->pluck('count', 'month');

        $labels     = $months->map(fn($m) => $m->translatedFormat('M Y'))->toArray();
        $loansData  = $months->map(fn($m) => $loans[$m->format('Y-m')] ?? 0)->toArray();
        $returnData = $months->map(fn($m) => $returned[$m->format('Y-m')] ?? 0)->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Emprunts',
                    'data'            => $loansData,
                    'borderColor'     => '#6366F1',
                    'backgroundColor' => '#6366F120',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Retours',
                    'data'            => $returnData,
                    'borderColor'     => '#10B981',
                    'backgroundColor' => '#10B98120',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}