<?php
// app/Filament/Widgets/QuickNavigationWidget.php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickNavigationWidget extends Widget
{
    protected static ?int $sort = 0; // En premier
    protected static string $view = 'filament.widgets.quick-navigation-widget';
    protected int | string | array $columnSpan = 'full';


    
}
