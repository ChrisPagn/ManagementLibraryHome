<?php
// app/Filament/Resources/ProfileResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Profil';
    protected static ?string $pluralModelLabel = 'Profils familiaux';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identité')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Prénom / Surnom')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('role')
                        ->label('Rôle')
                        ->options([
                            'admin'  => '👑 Administrateur',
                            'member' => '👤 Membre',
                        ])
                        ->default('member')
                        ->required(),

                    Forms\Components\FileUpload::make('avatar')
                        ->label('Avatar')
                        ->image()
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('200')
                        ->imageResizeTargetHeight('200')
                        ->directory('avatars'),
                ]),

            Forms\Components\Section::make('Sécurité')
                ->schema([
                    Forms\Components\TextInput::make('pin')
                        ->label('Code PIN (4 chiffres)')
                        ->password()
                        ->maxLength(4)
                        ->minLength(4)
                        ->numeric()
                        ->helperText('Optionnel — permet au membre de s\'identifier sur son profil'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Rôle')
                    ->colors([
                        'warning' => 'admin',
                        'primary' => 'member',
                    ]),

                Tables\Columns\TextColumn::make('loans_count')
                    ->label('Prêts actifs')
                    ->counts('activeLoans')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit'   => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}