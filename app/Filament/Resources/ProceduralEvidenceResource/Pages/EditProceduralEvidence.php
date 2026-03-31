<?php

namespace App\Filament\Resources\ProceduralEvidenceResource\Pages;

use App\Filament\Resources\ProceduralEvidenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProceduralEvidence extends EditRecord
{
    protected static string $resource = ProceduralEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
