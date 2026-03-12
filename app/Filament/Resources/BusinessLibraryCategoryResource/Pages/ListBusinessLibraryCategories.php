<?php

namespace App\Filament\Resources\BusinessLibraryCategoryResource\Pages;

use App\Filament\Resources\BusinessLibraryCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessLibraryCategories extends ListRecords
{
    protected static string $resource = BusinessLibraryCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
