<?php
// app/Filament/Widgets/OverdueLoansWidget.php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueLoansWidget extends BaseWidget
{
    protected static ?string $heading = '⚠️ Prêts en retard';
    protected static ?int    $sort    = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::with(['item', 'profile'])
                    ->whereNull('returned_at')
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now())
                    ->orderBy('due_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('item.title')
                    ->label('Item')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('profile.name')
                    ->label('Profil')
                    ->badge(),

                Tables\Columns\TextColumn::make('loaned_at')
                    ->label('Prêté le')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Retour prévu')
                    ->date('d/m/Y')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Retard')
                    ->state(fn (Loan $record) =>
                        now()->diffInDays($record->due_at) . ' jours'
                    )
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('return')
                    ->label('Marquer rendu')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Loan $record) => $record->markAsReturned()),
            ])
            ->emptyStateHeading('Aucun prêt en retard')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateDescription('Tout est rentré à temps ! ✅');
    }
}