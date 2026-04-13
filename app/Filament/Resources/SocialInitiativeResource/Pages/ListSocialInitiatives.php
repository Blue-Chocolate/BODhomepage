<?php

namespace App\Filament\Resources\SocialInitiativeResource\Pages;

use App\Filament\Resources\SocialInitiativeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialInitiatives extends ListRecords
{
    protected static string $resource = SocialInitiativeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}