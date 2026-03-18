<?php
// app/Filament/Resources/ItemResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use App\Services\ImportService;
use App\Services\ItemService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Item';
    protected static ?string $pluralModelLabel = 'Médiathèque';

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ─── Import ISBN ──────────────────────────────────────────────────
            Forms\Components\Section::make('Import automatique')
                ->description('Remplissage automatique via ISBN (livres & BD)')
                ->schema([
                    Forms\Components\TextInput::make('isbn_search')
                        ->label('ISBN')
                        ->placeholder('Ex: 9782070612758')
                        ->helperText('Saisis l\'ISBN puis clique sur Importer')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('importIsbn')
                                ->label('Importer')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('primary')
                                ->action(function (Forms\Get $get, Forms\Set $set) {

                                    $isbn = $get('isbn_search');

                                    if (empty($isbn)) {
                                        Notification::make()
                                            ->title('ISBN manquant')
                                            ->body('Saisis un ISBN avant d\'importer.')
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    // Appel ImportService
                                    $importService = app(ImportService::class);
                                    $data = app(\App\Importers\OpenLibraryImporter::class)
                                                ->fetchByIsbn($isbn);

                                    if (! $data) {
                                        Notification::make()
                                            ->title('ISBN introuvable')
                                            ->body("Aucun résultat pour l'ISBN : {$isbn}")
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    // Détection de doublons
                                    $itemService = app(ItemService::class);
                                    $duplicates  = $itemService->findDuplicates([
                                        'isbn'   => $isbn,
                                        'title'  => $data['title'],
                                        'author' => $data['author'],
                                    ]);

                                    if ($duplicates->isNotEmpty()) {
                                        $titles = $duplicates->pluck('title')->implode(', ');
                                        Notification::make()
                                            ->title('⚠️ Doublon détecté !')
                                            ->body("Un item similaire existe déjà : {$titles}. Tu peux continuer ou annuler.")
                                            ->warning()
                                            ->persistent() // reste affiché jusqu'à fermeture manuelle
                                            ->send();
                                    }

                                    // Remplissage automatique du formulaire
                                    $set('title', $data['title']);
                                    $set('subtitle', $data['subtitle']);
                                    $set('description', $data['description']);
                                    $set('author', $data['author']);
                                    $set('publisher', $data['publisher']);
                                    $set('published_year', $data['published_year']);
                                    $set('language', $data['language']);
                                    $set('isbn', $isbn);
                                    $set('cover', $data['cover_url']);

                                    Notification::make()
                                        ->title('✅ Import réussi !')
                                        ->body("« {$data['title']} » importé. Vérifie et sauvegarde.")
                                        ->success()
                                        ->send();
                                }),
                        ),
                ])
                ->collapsible(),

            // ─── Informations principales ─────────────────────────────────────
            Forms\Components\Section::make('Informations principales')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->columnSpan(2)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('subtitle')
                        ->label('Sous-titre')
                        ->columnSpan(2)
                        ->maxLength(255),

                    Forms\Components\Select::make('item_type_id')
                        ->label('Type')
                        ->relationship('type', 'name')
                        ->required()
                        ->preload()
                        ->searchable(),

                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'available' => '✅ Disponible',
                            'borrowed'  => '📤 Emprunté',
                            'lost'      => '❌ Perdu',
                        ])
                        ->default('available')
                        ->required(),
                ]),

            // ─── Détails ──────────────────────────────────────────────────────
            Forms\Components\Section::make('Détails')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('author')
                        ->label('Auteur / Développeur')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('publisher')
                        ->label('Éditeur')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('published_year')
                        ->label('Année de publication')
                        ->numeric()
                        ->minValue(1800)
                        ->maxValue(now()->year),

                    Forms\Components\TextInput::make('language')
                        ->label('Langue')
                        ->placeholder('fr, en, ...')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('isbn')
                        ->label('ISBN (stocké)')
                        ->maxLength(20)
                        ->unique(ignoreRecord: true)
                        ->helperText('Rempli automatiquement à l\'import'),
                ]),

            // ─── Description & Couverture ─────────────────────────────────────
            Forms\Components\Section::make('Description & Couverture')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(4),

                    Forms\Components\FileUpload::make('cover')
                        ->label('Image de couverture')
                        ->image()
                        ->directory('covers')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('2:3'),
                ]),

            // ─── Tags ─────────────────────────────────────────────────────────
            Forms\Components\Section::make('Tags')
                ->schema([
                    Forms\Components\Select::make('tags')
                        ->label('Tags')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('')
                    ->height(60)
                    ->width(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Auteur')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('published_year')
                    ->label('Année')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'borrowed',
                        'danger'  => 'lost',
                    ]),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Avis')
                    ->counts('reviews')
                    ->sortable(),

                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Note moy.')
                    ->state(fn (Item $record) =>
                        $record->averageRating()
                            ? number_format($record->averageRating(), 1) . ' ★'
                            : '—'
                    )
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('item_type_id')
                    ->label('Type')
                    ->relationship('type', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'available' => 'Disponible',
                        'borrowed'  => 'Emprunté',
                        'lost'      => 'Perdu',
                    ]),
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
            'index'  => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit'   => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}