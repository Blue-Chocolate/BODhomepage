<?php

namespace App\Filament\Resources\BusinessLibraryResource\Pages;

use App\Filament\Resources\BusinessLibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessLibrary extends EditRecord
{
    protected static string $resource = BusinessLibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
