<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalSolutionLinkResource\Pages;
use App\Models\DigitalSolutionLink;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput as NumberInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DigitalSolutionLinkResource extends Resource
{
    protected static ?string $model = DigitalSolutionLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'روابط الحلول الرقمية';

    protected static ?string $modelLabel = 'رابط';

    protected static ?string $pluralModelLabel = 'روابط الحلول الرقمية';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('label')
                ->label('الاسم بالعربي')
                ->required()
                ->maxLength(255),

            TextInput::make('label_en')
                ->label('الاسم بالانجليزي')
                ->maxLength(255),

            TextInput::make('url')
                ->label('الرابط')
                ->required()
                ->url()
                ->maxLength(255),

            TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),

            Toggle::make('open_in_new_tab')
                ->label('فتح في تبويب جديد')
                ->default(false),

            Toggle::make('is_active')
                ->label('مفعل')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('الاسم بالعربي')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label_en')
                    ->label('الاسم بالانجليزي')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('url')
                    ->label('الرابط')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                IconColumn::make('open_in_new_tab')
                    ->label('تبويب جديد')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDigitalSolutionLinks::route('/'),
            'create' => Pages\CreateDigitalSolutionLink::route('/create'),
            'edit'   => Pages\EditDigitalSolutionLink::route('/{record}/edit'),
        ];
    }
}