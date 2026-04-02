<?php

namespace App\Filament\Resources\AnnualPlanResource\Pages;
 
use App\Filament\Resources\AnnualPlanResource;
use Filament\Resources\Pages\CreateRecord;
 
class CreateAnnualPlan extends CreateRecord
{
    protected static string $resource = AnnualPlanResource::class;
 
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}