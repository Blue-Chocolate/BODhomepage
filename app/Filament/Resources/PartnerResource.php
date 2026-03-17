<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'الشركاء';
    protected static ?string $modelLabel = 'شريك';
    protected static ?string $pluralModelLabel = 'الشركاء';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                    if ($operation === 'create') {
                        $set('slug', Str::slug($state));
                    }
                }),

            TextInput::make('slug')
                ->label('الرابط المختصر')
                ->required()
                ->maxLength(255)
                ->unique(Partner::class, 'slug', ignoreRecord: true),

            TextInput::make('website_url')
                ->label('رابط الموقع')
                ->url()
                ->maxLength(255)
                ->placeholder('https://example.com')
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('الوصف')
                ->rows(4)
                ->columnSpanFull(),

            FileUpload::make('logo_path')
                ->label('الشعار')
                ->image()
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('16:9')
                ->directory('partners/logos')
                ->visibility('public')
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

                ImageColumn::make('logo_path')
                    ->label('الشعار')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.png')),

                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم النسخ'),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('لا يوجد وصف'),

                TextColumn::make('website_url')
                    ->label('الموقع')
                    ->url(fn($record) => $record->website_url)
                    ->openUrlInNewTab()
                    ->limit(40)
                    ->placeholder('لا يوجد موقع'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y')
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
            'index'  => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit'   => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}