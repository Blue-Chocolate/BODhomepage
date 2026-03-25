<?php

namespace App\Filament\Resources\FooterNavGroupResource\Pages;

use App\Filament\Resources\FooterNavGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterNavGroup extends EditRecord
{
    protected static string $resource = FooterNavGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
