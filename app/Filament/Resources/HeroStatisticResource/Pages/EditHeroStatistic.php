<?php

namespace App\Filament\Resources\HeroStatisticResource\Pages;

use App\Filament\Resources\HeroStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeroStatistic extends EditRecord
{
    protected static string $resource = HeroStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
