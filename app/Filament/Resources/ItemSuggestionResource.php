<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemSuggestionResource\Pages;
use App\Models\Item;
use App\Models\ItemSuggestion;
use App\Models\ItemType;
use App\Importers\OpenLibraryImporter;
use App\Services\ItemService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemSuggestionResource extends Resource
{
    protected static ?string $model = ItemSuggestion::class;
    protected static ?string $navigationIcon  = 'heroicon-o-light-bulb';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $modelLabel        = 'Suggestion';
    protected static ?string $pluralModelLabel  = 'Suggestions d\'items';

    public static function getNavigationBadge(): ?string
    {
        $count = ItemSuggestion::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Suggestion')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->disabled()
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('author')
                        ->label('Auteur')
                        ->disabled(),

                    Forms\Components\TextInput::make('isbn')
                        ->label('ISBN')
                        ->disabled(),

                    Forms\Components\Textarea::make('note')
                        ->label('Message du membre')
                        ->disabled()
                        ->columnSpan(2),

                    Forms\Components\Select::make('profile_id')
                        ->label('Suggéré par')
                        ->relationship('profile', 'name')
                        ->disabled(),
                ]),

            Forms\Components\Section::make('Décision admin')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'pending'  => '⏳ En attente',
                            'approved' => '✅ Approuvé',
                            'rejected' => '❌ Refusé',
                        ])
                        ->required(),

                    Forms\Components\Textarea::make('admin_note')
                        ->label('Réponse au membre')
                        ->placeholder('Ex: Super idée ! Je vais l\'acheter bientôt.')
                        ->columnSpan(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('profile.name')
                    ->label('Membre')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre suggéré')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('author')
                    ->label('Auteur')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('note')
                    ->label('Message')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending'  => '⏳ En attente',
                        'approved' => '✅ Approuvé',
                        'rejected' => '❌ Refusé',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Refusé',
                    ]),

                Tables\Filters\Filter::make('pending')
                    ->label('En attente uniquement')
                    ->query(fn ($query) => $query->where('status', 'pending'))
                    ->default(),
            ])
            ->actions([

                // ── Importer via ISBN ─────────────────────────────────
                Tables\Actions\Action::make('importIsbn')
                ->label('Importer via ISBN')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(fn (ItemSuggestion $record) =>
                    $record->isPending() && ! empty($record->isbn)
                )
                ->mountUsing(function (Forms\ComponentContainer $form, ItemSuggestion $record) {
                    // Tente l'import API au moment d'ouvrir le modal
                    $importer = app(OpenLibraryImporter::class);
                    $data = $importer->fetchByIsbnWithFallback($record->isbn);

                    // Pré-remplit avec les données API ou la suggestion
                    $form->fill([
                        'title'          => $data['title']          ?? $record->title,
                        'author'         => $data['author']         ?? $record->author,
                        'publisher'      => $data['publisher']      ?? null,
                        'published_year' => $data['published_year'] ?? null,
                        'description'    => $data['description']    ?? null,
                        'language'       => $data['language']       ?? null,
                        'cover_url'      => $data['cover_url']      ?? null,
                        'api_found'      => $data !== null,
                    ]);
                })
                ->form([
                    Forms\Components\Placeholder::make('api_status')
                        ->label('')
                        ->content(fn (Forms\Get $get) =>
                            $get('api_found')
                                ? '✅ Données trouvées via API — vérifie et complète si besoin.'
                                : '⚠️ ISBN introuvable sur les APIs. Complète manuellement.'
                        ),

                    Forms\Components\Hidden::make('api_found'),
                    Forms\Components\Hidden::make('cover_url'),

                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->required(),

                    Forms\Components\TextInput::make('author')
                        ->label('Auteur'),

                    Forms\Components\TextInput::make('publisher')
                        ->label('Éditeur'),

                    Forms\Components\TextInput::make('published_year')
                        ->label('Année de publication')
                        ->numeric(),

                    Forms\Components\Select::make('item_type_id')
                        ->label('Type de média')
                        ->options(\App\Models\ItemType::pluck('name', 'id'))
                        ->required(),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3),

                    Forms\Components\TextInput::make('language')
                        ->label('Langue')
                        ->placeholder('fr, en...'),
                ])
                ->action(function (ItemSuggestion $record, array $data) {

                    // Télécharge la couverture si URL disponible
                    $coverPath = null;
                    if (! empty($data['cover_url'])) {
                        try {
                            $response = \Illuminate\Support\Facades\Http::timeout(10)
                                            ->get($data['cover_url']);
                            if ($response->successful()) {
                                $isbn      = preg_replace('/[^0-9X]/', '', strtoupper($record->isbn));
                                $coverPath = "covers/isbn_{$isbn}.jpg";
                                \Illuminate\Support\Facades\Storage::disk('public')
                                    ->put($coverPath, $response->body());
                            }
                        } catch (\Exception $e) {
                            // Continue sans couverture
                        }
                    }

                    // Vérifie les doublons
                    $itemService = app(ItemService::class);
                    $duplicates  = $itemService->findDuplicates([
                        'isbn'   => $record->isbn,
                        'title'  => $data['title'],
                        'author' => $data['author'] ?? null,
                    ]);

                    if ($duplicates->isNotEmpty()) {
                        Notification::make()
                            ->title('⚠️ Doublon détecté !')
                            ->body("Un item similaire existe déjà : « {$duplicates->first()->title} ».")
                            ->warning()
                            ->persistent()
                            ->send();
                        $record->update([
                            'status'     => 'approved',
                            'admin_note' => 'Item déjà présent dans la médiathèque.',
                        ]);
                        return;
                    }

                    // Crée l'item
                    $item = Item::create([
                        'item_type_id'   => $data['item_type_id'],
                        'title'          => $data['title'],
                        'author'         => $data['author'] ?? null,
                        'publisher'      => $data['publisher'] ?? null,
                        'published_year' => $data['published_year'] ?? null,
                        'description'    => $data['description'] ?? null,
                        'language'       => $data['language'] ?? null,
                        'isbn'           => $record->isbn,
                        'cover'          => $coverPath,
                        'status'         => 'available',
                    ]);

                    // Approuve la suggestion
                    $record->update([
                        'status'     => 'approved',
                        'admin_note' => "Ajouté à la médiathèque : « {$item->title} ».",
                    ]);

                    Notification::make()
                        ->title('✅ Item créé et approuvé !')
                        ->body("« {$item->title} » a été ajouté à la médiathèque.")
                        ->success()
                        ->send();
                }),

                // ── Approuver manuellement ────────────────────────────
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ItemSuggestion $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Approuver cette suggestion ?')
                    ->modalDescription(fn (ItemSuggestion $record) =>
                        "Approuver la suggestion de {$record->profile->name} : « {$record->title} »"
                    )
                    ->action(function (ItemSuggestion $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->title('Suggestion approuvée ✅')
                            ->success()
                            ->send();
                    }),

                // ── Refuser ───────────────────────────────────────────
                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ItemSuggestion $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Raison du refus (optionnel)')
                            ->placeholder('Ex: On l\'a déjà en version numérique.'),
                    ])
                    ->action(function (ItemSuggestion $record, array $data) {
                        $record->update([
                            'status'     => 'rejected',
                            'admin_note' => $data['admin_note'] ?? null,
                        ]);
                        Notification::make()
                            ->title('Suggestion refusée')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('Détails'),
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
            'index'  => Pages\ListItemSuggestions::route('/'),
            'create' => Pages\CreateItemSuggestion::route('/create'),
            'edit'   => Pages\EditItemSuggestion::route('/{record}/edit'),
        ];
    }
}