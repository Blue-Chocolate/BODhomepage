<?php

namespace App\Filament\Resources\BusinessLibraryResource\Pages;

use App\Filament\Resources\BusinessLibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessLibraries extends ListRecords
{
    protected static string $resource = BusinessLibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
