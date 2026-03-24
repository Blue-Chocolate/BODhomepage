<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/TestimonialResource.php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make()->schema([
            Forms\Components\TextInput::make('name')->label('الاسم عربي')->required(),
            Forms\Components\TextInput::make('name_en')->label('الاسم إنجليزي'),
            Forms\Components\TextInput::make('organization')->label('المنظمة عربي'),
            Forms\Components\TextInput::make('organization_en')->label('المنظمة إنجليزي'),
            Forms\Components\Textarea::make('quote')->label('الاقتباس عربي')->required(),
            Forms\Components\Textarea::make('quote_en')->label('الاقتباس إنجليزي'),
            Forms\Components\TextInput::make('rating')->label('التقييم (1-5)')->numeric()->minValue(1)->maxValue(5)->default(5),
            Forms\Components\FileUpload::make('image_path')->label('الصورة')->image()->directory('testimonials'),
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
            Tables\Columns\ImageColumn::make('image_path')->label('صورة')->circular(),
            Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
            Tables\Columns\TextColumn::make('organization')->label('المنظمة'),
            Tables\Columns\TextColumn::make('rating')->label('التقييم'),
            Tables\Columns\IconColumn::make('is_active')->label('مفعّل')->boolean(),
        ])
        ->reorderable('sort_order')
        ->defaultSort('sort_order')
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->headerActions([Tables\Actions\CreateAction::make()]);
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
