<?php

namespace App\Filament\Resources\BlogCateogryResource\Pages;

use App\Filament\Resources\BlogCateogryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogCateogry extends EditRecord
{
    protected static string $resource = BlogCateogryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
