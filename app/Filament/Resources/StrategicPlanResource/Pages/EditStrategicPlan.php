<?php

namespace App\Filament\Resources\StrategicPlanResource\Pages;

use App\Filament\Resources\StrategicPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStrategicPlan extends \Filament\Resources\Pages\EditRecord {
    protected static string $resource = StrategicPlanResource::class;
    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
}
