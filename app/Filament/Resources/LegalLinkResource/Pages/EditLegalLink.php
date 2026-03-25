<?php

namespace App\Filament\Resources\LegalLinkResource\Pages;

use App\Filament\Resources\LegalLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLegalLink extends EditRecord
{
    protected static string $resource = LegalLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
