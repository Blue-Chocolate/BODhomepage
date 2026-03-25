<?php

// app/Filament/Resources/FooterNavGroupResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\FooterNavGroupResource\Pages;
use App\Models\FooterNavGroup;
use App\Models\FooterNavLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FooterNavGroupResource extends Resource
{
    protected static ?string $model           = FooterNavGroup::class;
    protected static ?string $navigationIcon  = 'heroicon-o-bars-3-bottom-right';
    protected static ?string $navigationLabel = 'قائمة الفوتر';
    protected static ?string $navigationGroup = 'الموقع';
    protected static ?string $modelLabel      = 'عمود';
    protected static ?string $pluralModelLabel = 'أعمدة الفوتر';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات العمود')->schema([
                Forms\Components\TextInput::make('title')->label('العنوان عربي')->required(),
                Forms\Components\TextInput::make('title_en')->label('العنوان إنجليزي'),
                Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('مفعّل')->default(true),
            ])->columns(2),

            // روابط العمود inline
            Forms\Components\Section::make('الروابط')->schema([
                Forms\Components\Repeater::make('links')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('label')->label('الاسم عربي')->required(),
                        Forms\Components\TextInput::make('label_en')->label('الاسم إنجليزي'),
                        Forms\Components\TextInput::make('url')->label('الرابط')->required(),
                        Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                        Forms\Components\Toggle::make('open_in_new_tab')->label('تبويب جديد'),
                        Forms\Components\Toggle::make('is_active')->label('مفعّل')->default(true),
                    ])
                    ->columns(3)
                    ->reorderable('sort_order')
                    ->addActionLabel('إضافة رابط')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(60),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
                Tables\Columns\TextColumn::make('links_count')->label('الروابط')->counts('links'),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFooterNavGroups::route('/'),
            'create' => Pages\CreateFooterNavGroup::route('/create'),
            'edit'   => Pages\EditFooterNavGroup::route('/{record}/edit'),
        ];
    }
}