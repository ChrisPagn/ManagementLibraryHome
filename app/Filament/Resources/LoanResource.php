<?php
// app/Filament/Resources/LoanResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationGroup = 'Prêts';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Prêt';
    protected static ?string $pluralModelLabel = 'Gestion des prêts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Prêt')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('item_id')
                        ->label('Item')
                        ->relationship('item', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('profile_id')
                        ->label('Profil familial')
                        ->relationship('profile', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('loaned_at')
                        ->label('Date de prêt')
                        ->default(now())
                        ->required(),

                    Forms\Components\DatePicker::make('due_at')
                        ->label('Retour prévu'),

                    Forms\Components\DatePicker::make('returned_at')
                        ->label('Retourné le'),

                    Forms\Components\TextInput::make('borrower_name')
                        ->label('Emprunté par (externe)')
                        ->helperText('Si prêté à quelqu\'un hors famille'),

                    Forms\Components\Textarea::make('note')
                        ->label('Note')
                        ->columnSpan(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.title')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profile.name')
                    ->label('Profil')
                    ->badge(),

                Tables\Columns\TextColumn::make('loaned_at')
                    ->label('Prêté le')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Retour prévu')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('returned_at')
                    ->label('Rendu ?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock'),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Prêts en cours')
                    ->query(fn ($query) => $query->whereNull('returned_at'))
                    ->default(),

                Tables\Filters\Filter::make('overdue')
                    ->label('En retard')
                    ->query(fn ($query) => $query->whereNull('returned_at')
                        ->whereNotNull('due_at')
                        ->where('due_at', '<', now())
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Action rapide "Marquer comme rendu"
                Tables\Actions\Action::make('return')
                    ->label('Rendu')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Loan $record) => ! $record->isReturned())
                    ->requiresConfirmation()
                    ->action(fn (Loan $record) => $record->markAsReturned()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit'   => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}