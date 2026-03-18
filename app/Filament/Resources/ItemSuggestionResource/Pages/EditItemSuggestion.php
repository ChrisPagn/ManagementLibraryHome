<?php

namespace App\Filament\Resources\ItemSuggestionResource\Pages;

use App\Filament\Resources\ItemSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemSuggestion extends EditRecord
{
    protected static string $resource = ItemSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
