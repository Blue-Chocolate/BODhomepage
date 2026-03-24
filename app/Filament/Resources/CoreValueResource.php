<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoreValueResource\Pages;
use App\Filament\Resources\CoreValueResource\RelationManagers;
use App\Models\CoreValue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoreValueResource extends Resource
{
    protected static ?string $model = CoreValue::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

// app/Filament/Resources/CoreValueResource.php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make()->schema([
            Forms\Components\TextInput::make('title')->label('العنوان عربي')->required(),
            Forms\Components\TextInput::make('title_en')->label('العنوان إنجليزي'),
            Forms\Components\Textarea::make('description')->label('الوصف عربي'),
            Forms\Components\Textarea::make('description_en')->label('الوصف إنجليزي'),
            Forms\Components\TextInput::make('icon')->label('الأيقونة')->placeholder('heroicon-o-star'),
            Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('مفعّل')->default(true),
        ])->columns(2),
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(60),
            Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
            Tables\Columns\IconColumn::make('is_active')->label('مفعّل')->boolean(),
        ])
        ->reorderable('sort_order')
        ->defaultSort('sort_order')
        ->actions([
            Tables\Actions\EditAction::make()->after(fn() => cache()->forget('who_are_we')),
            Tables\Actions\DeleteAction::make()->after(fn() => cache()->forget('who_are_we')),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()->after(fn() => cache()->forget('who_are_we')),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoreValues::route('/'),
            'create' => Pages\CreateCoreValue::route('/create'),
            'edit' => Pages\EditCoreValue::route('/{record}/edit'),
        ];
    }
}
