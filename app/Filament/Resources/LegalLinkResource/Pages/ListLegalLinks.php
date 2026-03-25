<?php

namespace App\Filament\Resources\LegalLinkResource\Pages;

use App\Filament\Resources\LegalLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLegalLinks extends ListRecords
{
    protected static string $resource = LegalLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
