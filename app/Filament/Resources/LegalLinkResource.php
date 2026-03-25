<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalLinkResource\Pages;
use App\Filament\Resources\LegalLinkResource\RelationManagers;
use App\Models\LegalLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LegalLinkResource extends Resource
{
    protected static ?string $model = LegalLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Footer';

    // app/Filament/Resources/LegalLinkResource.php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make()->schema([
            Forms\Components\TextInput::make('label')->label('الاسم عربي')->required(),
            Forms\Components\TextInput::make('label_en')->label('الاسم إنجليزي'),
            Forms\Components\TextInput::make('url')->label('الرابط أو PDF')->required(),
            Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
            Forms\Components\Toggle::make('open_in_new_tab')->label('فتح في تبويب جديد')->default(false),
            Forms\Components\Toggle::make('is_active')->label('مفعّل')->default(true),
        ])->columns(2),
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(60),
            Tables\Columns\TextColumn::make('label')->label('الاسم')->searchable(),
            Tables\Columns\TextColumn::make('url')->label('الرابط')->limit(40),
            Tables\Columns\IconColumn::make('is_active')->label('مفعّل')->boolean(),
        ])
        ->reorderable('sort_order')
        ->defaultSort('sort_order')
        ->actions([
            Tables\Actions\EditAction::make()->after(fn() => cache()->forget('footer_data')),
            Tables\Actions\DeleteAction::make()->after(fn() => cache()->forget('footer_data')),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()->after(fn() => cache()->forget('footer_data')),
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
            'index' => Pages\ListLegalLinks::route('/'),
            'create' => Pages\CreateLegalLink::route('/create'),
            'edit' => Pages\EditLegalLink::route('/{record}/edit'),
        ];
    }
}
