<?php
// app/Filament/Resources/WishlistResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistResource\Pages;
use App\Models\Wishlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;
    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int    $navigationSort  = 4;
    protected static ?string $modelLabel        = 'Souhait';
    protected static ?string $pluralModelLabel  = 'Liste de souhaits';

    public static function getNavigationBadge(): ?string
    {
        $count = Wishlist::where('is_acquired', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Item souhaité')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('author')
                        ->label('Auteur'),

                    Forms\Components\TextInput::make('isbn')
                        ->label('ISBN'),

                    Forms\Components\Select::make('item_type_id')
                        ->label('Type de média')
                        ->relationship('type', 'name')
                        ->preload()
                        ->searchable(),

                    Forms\Components\Select::make('profile_id')
                        ->label('Pour qui ?')
                        ->relationship('profile', 'name')
                        ->required()
                        ->preload(),

                    Forms\Components\Select::make('priority')
                        ->label('Priorité')
                        ->options([
                            'high'   => '🔴 Haute',
                            'medium' => '🟡 Moyenne',
                            'low'    => '🟢 Basse',
                        ])
                        ->default('medium')
                        ->required(),

                    Forms\Components\TextInput::make('estimated_price')
                        ->label('Prix estimé (€)')
                        ->numeric()
                        ->prefix('€'),

                    Forms\Components\Textarea::make('note')
                        ->label('Note')
                        ->columnSpan(2),

                    Forms\Components\Toggle::make('is_acquired')
                        ->label('Acquis ?')
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if ($state) {
                                $set('acquired_at', now()->toDateString());
                            }
                        }),

                    Forms\Components\DatePicker::make('acquired_at')
                        ->label('Date d\'acquisition')
                        ->visible(fn (Forms\Get $get) => $get('is_acquired')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('priority', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('profile.name')
                    ->label('Pour')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('author')
                    ->label('Auteur')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Type')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->color(fn (string $state) => match($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'success',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'high'   => '🔴 Haute',
                        'medium' => '🟡 Moyenne',
                        'low'    => '🟢 Basse',
                        default  => $state,
                    }),

                Tables\Columns\TextColumn::make('estimated_price')
                    ->label('Prix')
                    ->money('EUR')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_acquired')
                    ->label('Acquis')
                    ->boolean(),

                Tables\Columns\TextColumn::make('acquired_at')
                    ->label('Acquis le')
                    ->date('d/m/Y')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('not_acquired')
                    ->label('Non acquis uniquement')
                    ->query(fn ($query) => $query->where('is_acquired', false))
                    ->default(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorité')
                    ->options([
                        'high'   => 'Haute',
                        'medium' => 'Moyenne',
                        'low'    => 'Basse',
                    ]),

                Tables\Filters\SelectFilter::make('profile_id')
                    ->label('Profil')
                    ->relationship('profile', 'name'),
            ])
            ->actions([
                // Action rapide "Marquer comme acquis"
                Tables\Actions\Action::make('acquire')
                    ->label('Acquis !')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Wishlist $record) => ! $record->is_acquired)
                    ->requiresConfirmation()
                    ->modalHeading('Marquer comme acquis ?')
                    ->modalDescription(fn (Wishlist $record) =>
                        "« {$record->title} » a été acquis ?"
                    )
                    ->action(function (Wishlist $record) {
                        $record->markAsAcquired();
                        Notification::make()
                            ->title('✅ Item acquis !')
                            ->body("« {$record->title} » a été marqué comme acquis.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWishlists::route('/'),
            'create' => Pages\CreateWishlist::route('/create'),
            'edit'   => Pages\EditWishlist::route('/{record}/edit'),
        ];
    }
}