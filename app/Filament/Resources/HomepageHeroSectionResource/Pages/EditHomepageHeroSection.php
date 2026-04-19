<?php

namespace App\Filament\Resources\HomepageHeroSectionResource\Pages;

use App\Filament\Resources\HomepageHeroSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomepageHeroSection extends EditRecord
{
    protected static string $resource = HomepageHeroSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
