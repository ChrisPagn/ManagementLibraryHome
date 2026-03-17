<?php
// app/Filament/Resources/CollectionResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $modelLabel        = 'Collection';
    protected static ?string $pluralModelLabel  = 'Collections & Séries';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informations')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom de la série')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->columnSpan(2),

                    // Slug : visible mais en lecture seule en édition
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->readOnly()  // généré automatiquement, pas modifiable
                        ->helperText('Généré automatiquement depuis le nom'),

                    Forms\Components\Select::make('item_type_id')
                        ->label('Type de média')
                        ->relationship('type', 'name')
                        ->required()
                        ->preload()
                        ->searchable(),

                    Forms\Components\TextInput::make('author')
                        ->label('Auteur / Créateur')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('total_volumes')
                        ->label('Nombre de tomes total')
                        ->numeric()
                        ->minValue(1)
                        ->helperText('Laisser vide si le total est inconnu'),

                    Forms\Components\Toggle::make('is_complete')
                        ->label('Série terminée ?')
                        ->default(false),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->columnSpan(2)
                        ->rows(3),

                    Forms\Components\FileUpload::make('cover')
                        ->label('Couverture')
                        ->image()
                        ->directory('covers/collections')
                        ->columnSpan(2),
                ]),

            Forms\Components\Section::make('Tomes possédés')
        ->description('Associe les items de ta médiathèque à cette collection')
        ->schema([

            // Bouton de recherche automatique
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('searchSeries')
                    ->label('🔍 Rechercher les tomes via Open Library')
                    ->color('primary')
                    ->icon('heroicon-o-magnifying-glass')
                    ->visible(fn (Forms\Get $get) => ! empty($get('name')))
                    ->action(function (Forms\Get $get, Forms\Set $set) {

                        $seriesName = $get('name');

                        if (empty($seriesName)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Nom manquant')
                                ->body('Saisis d\'abord le nom de la série.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $collectionService = app(\App\Services\CollectionService::class);
                        $matches = $collectionService->findExistingItemsForSeries($seriesName);

                        $inLibrary = collect($matches)->filter(fn($m) => $m['in_library']);
                        $total     = collect($matches)->count();

                        // Stocke les résultats pour affichage
                        $set('_search_results', json_encode($matches));
                        $set('_total_found', $total);

                        // Pré-sélectionne les items déjà en bibliothèque
                        $existingIds = $inLibrary->pluck('existing_item.id')->toArray();
                        $set('items', $existingIds);

                        // Met à jour total_volumes si trouvé
                        if ($total > 0 && empty($get('total_volumes'))) {
                            $set('total_volumes', $total);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Recherche terminée')
                            ->body("{$total} tomes trouvés sur Open Library. " .
                                "{$inLibrary->count()} déjà dans ta bibliothèque et pré-sélectionnés.")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ]),

            // Résumé des résultats de recherche
            Forms\Components\Placeholder::make('_search_summary')
                ->label('Résultat de la recherche')
                ->content(function (Forms\Get $get) {
                    $results = $get('_search_results');
                    if (empty($results)) return 'Lance une recherche pour voir les tomes disponibles.';

                    $matches   = json_decode($results, true);
                    $inLibrary = collect($matches)->filter(fn($m) => $m['in_library'])->count();
                    $total     = count($matches);
                    $missing   = $total - $inLibrary;

                    return "📚 {$total} tomes trouvés — "
                        . "✅ {$inLibrary} dans ta bibliothèque — "
                        . "❌ {$missing} manquants";
                })
                ->visible(fn (Forms\Get $get) => ! empty($get('_search_results'))),

            // Sélection manuelle / confirmation des items
            Forms\Components\Select::make('items')
                ->label('Tomes rattachés à cette collection')
                ->relationship('items', 'title')
                ->multiple()
                ->preload()
                ->searchable()
                ->helperText('Les items de ta bibliothèque sont pré-sélectionnés après la recherche. Tu peux ajuster manuellement.'),
        ])
        ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('')
                    ->height(50)
                    ->width(35),

                Tables\Columns\TextColumn::make('name')
                    ->label('Série')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Auteur')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Possédés')
                    ->counts('items')
                    ->suffix(fn (Collection $record) =>
                        $record->total_volumes ? ' / ' . $record->total_volumes : ''
                    ),

                Tables\Columns\TextColumn::make('completion')
                    ->label('Complétion')
                    ->state(fn (Collection $record) =>
                        $record->total_volumes
                            ? $record->completionPercentage() . '%'
                            : 'N/A'
                    )
                    ->badge()
                    ->color(fn (Collection $record) => match(true) {
                        $record->completionPercentage() === 100 => 'success',
                        $record->completionPercentage() >= 50   => 'warning',
                        default                                  => 'danger',
                    }),

                Tables\Columns\IconColumn::make('is_complete')
                    ->label('Terminée')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('item_type_id')
                    ->label('Type')
                    ->relationship('type', 'name'),

                Tables\Filters\Filter::make('incomplete')
                    ->label('Collections incomplètes')
                    ->query(fn ($query) => $query->where('is_complete', false)),
            ])
            ->actions([
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
            'index'  => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit'   => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}