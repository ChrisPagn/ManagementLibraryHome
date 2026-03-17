<?php
// app/Filament/Pages/Dashboard.php — crée ce fichier

namespace App\Filament\Pages;

use App\Filament\Widgets\CollectionProgressWidget;
use App\Filament\Widgets\ItemsByTypeWidget;
use App\Filament\Widgets\LoansByMonthWidget;
use App\Filament\Widgets\OverdueLoansWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $title           = 'Tableau de bord';
    protected static ?int    $navigationSort  = -1; // Premier dans le menu

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            ItemsByTypeWidget::class,
            LoansByMonthWidget::class,
            OverdueLoansWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2; // 2 colonnes pour les graphiques
    }
}