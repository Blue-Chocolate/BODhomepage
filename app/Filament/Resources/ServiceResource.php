<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'الخدمات';

    public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make('بيانات الخدمة')->schema([
            Forms\Components\TextInput::make('title')->label('العنوان عربي')->required(),
            Forms\Components\TextInput::make('title_en')->label('العنوان إنجليزي'),
            Forms\Components\Textarea::make('description')->label('الوصف عربي'),
            Forms\Components\Textarea::make('description_en')->label('الوصف إنجليزي'),
            Forms\Components\TextInput::make('icon')->label('الأيقونة'),
            Forms\Components\FileUpload::make('image_path')->label('الصورة')->image()->directory('services'),
        ])->columns(2),

        Forms\Components\Section::make('زر CTA')->schema([
            Forms\Components\TextInput::make('cta_text')->label('نص الزر عربي'),
            Forms\Components\TextInput::make('cta_text_en')->label('نص الزر إنجليزي'),
            Forms\Components\TextInput::make('cta_url')->label('رابط الزر'),
        ])->columns(3),

        Forms\Components\Section::make('الإعدادات')->schema([
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
            Tables\Columns\ImageColumn::make('image_path')->label('صورة'),
            Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
            Tables\Columns\TextColumn::make('cta_url')->label('CTA')->limit(30),
            Tables\Columns\IconColumn::make('is_active')->label('مفعّل')->boolean(),
        ])
        ->reorderable('sort_order')
        ->defaultSort('sort_order')
        ->actions([
            Tables\Actions\EditAction::make()->after(fn() => cache()->forget('services_section')),
            Tables\Actions\DeleteAction::make()->after(fn() => cache()->forget('services_section')),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()->after(fn() => cache()->forget('services_section')),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
