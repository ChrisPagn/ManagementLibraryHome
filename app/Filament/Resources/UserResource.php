<?php
// app/Filament/Resources/UserResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 3;
    protected static ?string $modelLabel        = 'Utilisateur admin';
    protected static ?string $pluralModelLabel  = 'Utilisateurs admin';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Compte administrateur')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label('Nouveau mot de passe')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) =>
                            filled($state) ? Hash::make($state) : null
                        )
                        ->dehydrated(fn ($state) => filled($state))
                        ->revealable()
                        ->helperText('Laisse vide pour ne pas changer le mot de passe')
                        ->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copié !'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Vérifié le')
                    ->dateTime('d/m/Y')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}