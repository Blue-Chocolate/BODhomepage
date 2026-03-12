<?php

namespace App\Filament\Resources\BusinessLibraryCategoryResource\Pages;

use App\Filament\Resources\BusinessLibraryCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessLibraryCategory extends EditRecord
{
    protected static string $resource = BusinessLibraryCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
