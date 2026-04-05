<?php

namespace App\Filament\Resources\DigitalSolutionLinkResource\Pages;

use App\Filament\Resources\DigitalSolutionLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDigitalSolutionLink extends EditRecord
{
    protected static string $resource = DigitalSolutionLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}