<?php
// app/Filament/Widgets/ItemsByTypeWidget.php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\ItemType;
use Filament\Widgets\ChartWidget;

class ItemsByTypeWidget extends ChartWidget
{
    protected static ?string $heading = 'Médiathèque par type';
    protected static ?int    $sort    = 2;

    protected function getData(): array
    {
        $types = ItemType::withCount('items')->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Items',
                    'data'            => $types->pluck('items_count')->toArray(),
                    'backgroundColor' => [
                        '#6366F1', // Indigo  — Livres
                        '#F59E0B', // Amber   — BD
                        '#10B981', // Emerald — Jeux société
                        '#3B82F6', // Blue    — Jeux vidéo
                        '#EC4899', // Pink    — autres
                    ],
                ],
            ],
            'labels' => $types->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // camembert avec trou
    }
}