<?php

namespace App\Filament\Resources\HomepageHeroSectionResource\Pages;

use App\Filament\Resources\HomepageHeroSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomepageHeroSections extends ListRecords
{
    protected static string $resource = HomepageHeroSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}