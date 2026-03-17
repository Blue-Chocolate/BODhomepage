<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsResource\Pages;
use App\Models\ContactUs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'رسائل التواصل';
    protected static ?string $modelLabel = 'رسالة';
    protected static ?string $pluralModelLabel = 'رسائل التواصل';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255),

            TextInput::make('phone')
                ->label('رقم الهاتف')
                ->tel()
                ->maxLength(20),

            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('subject')
                ->label('الموضوع')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('message')
                ->label('الرسالة')
                ->required()
                ->rows(5)
                ->columnSpanFull(),

            Textarea::make('reply')
                ->label('الرد')
                ->rows(5)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),

                TextColumn::make('subject')
                    ->label('الموضوع')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('message')
                    ->label('الرسالة')
                    ->limit(80)
                    ->wrap(),

                TextColumn::make('reply')
                    ->label('الرد')
                    ->limit(80)
                    ->placeholder('لا يوجد رد')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                ViewAction::make()->label('عرض'),
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContactUs::route('/'),
            'create' => Pages\CreateContactUs::route('/create'),
            'edit'   => Pages\EditContactUs::route('/{record}/edit'),
        ];
    }
}