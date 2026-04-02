<?php
// ─── Pages/ListAnnualPlans.php ────────────────────────────────────────────────
namespace App\Filament\Resources\AnnualPlanResource\Pages;
 
use App\Filament\Resources\AnnualPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
 
class ListAnnualPlans extends ListRecords
{
    protected static string $resource = AnnualPlanResource::class;
 
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة خطة'),
        ];
    }
}