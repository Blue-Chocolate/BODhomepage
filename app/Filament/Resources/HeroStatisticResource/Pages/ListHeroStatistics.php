<?php

namespace App\Filament\Resources\HeroStatisticResource\Pages;

use App\Filament\Resources\HeroStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeroStatistics extends ListRecords
{
    protected static string $resource = HeroStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
