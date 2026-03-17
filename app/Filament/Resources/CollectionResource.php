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
                    Forms\Components\Select::make('items')
                        ->label('Ajouter des tomes')
                        ->relationship('items', 'title')
                        ->multiple()
                        ->preload()
                        ->searchable(),
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