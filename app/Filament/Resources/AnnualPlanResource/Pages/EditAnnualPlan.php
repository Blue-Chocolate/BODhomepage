<?php

namespace App\Filament\Resources\AnnualPlanResource\Pages;
 
use App\Filament\Resources\AnnualPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
 
class EditAnnualPlan extends EditRecord
{
    protected static string $resource = AnnualPlanResource::class;
 
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('عرض'),
            Actions\DeleteAction::make()->label('حذف'),
            Actions\ForceDeleteAction::make()->label('حذف نهائي'),
            Actions\RestoreAction::make()->label('استعادة'),
        ];
    }
 
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
 