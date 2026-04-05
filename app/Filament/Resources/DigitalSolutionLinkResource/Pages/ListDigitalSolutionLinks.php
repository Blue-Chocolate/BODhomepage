<?php

namespace App\Filament\Resources\DigitalSolutionLinkResource\Pages;

use App\Filament\Resources\DigitalSolutionLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDigitalSolutionLinks extends ListRecords
{
    protected static string $resource = DigitalSolutionLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}