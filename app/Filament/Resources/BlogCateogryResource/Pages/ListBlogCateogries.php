<?php

namespace App\Filament\Resources\BlogCateogryResource\Pages;

use App\Filament\Resources\BlogCateogryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogCateogries extends ListRecords
{
    protected static string $resource = BlogCateogryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
