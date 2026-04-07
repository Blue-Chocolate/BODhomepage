<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroStatisticResource\Pages;
use App\Models\HeroStatistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroStatisticResource extends Resource
{
    protected static ?string $model = HeroStatistic::class;

    protected static ?string $navigationIcon   = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup  = 'إدارة الموقع';
    protected static ?string $navigationLabel  = 'شريط الإحصاءات';
    protected static ?string $modelLabel       = 'إحصائية';
    protected static ?string $pluralModelLabel = 'إحصاءات الهيرو';
    protected static ?int    $navigationSort   = 11;

    // ─── FORM ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('الموقع المستهدف')->schema([
                Forms\Components\Select::make('site')
                    ->label('الموقع')
                    ->options([
                        'waleda'  => 'موقع والدة حلم',
                        'manzuma' => 'موقع المنظومة المجتمعية',
                        'both'    => 'كلا الموقعين',
                    ])
                    ->required()
                    ->default('waleda')
                    ->native(false),
            ])->compact(),

            Section::make('بيانات الإحصائية')->schema([
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('icon')
                        ->label('اسم الأيقونة (Heroicon)')
                        ->placeholder('user-group')
                        ->helperText('اسم أيقونة Heroicons بدون heroicon-o- — مثال: user-group')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('value')
                        ->label('القيمة')
                        ->required()
                        ->placeholder('150+')
                        ->maxLength(50)
                        ->helperText('النص الظاهر كرقم — مثال: 150+ أو 10,000'),
                ]),

                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('label_ar')
                        ->label('التسمية (عربي) *')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('label_en')
                        ->label('Label (English) *')
                        ->required()
                        ->maxLength(100),
                ]),
            ]),

            Section::make('الترتيب والحالة')->schema([
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('sort_order')
                        ->label('ترتيب العرض')
                        ->numeric()
                        ->default(0)
                        ->helperText('الأصغر يظهر أولاً'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('نشط')
                        ->default(true)
                        ->inline(false),
                ]),
            ]),
        ]);
    }

    // ─── TABLE ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label('الأيقونة')
                    ->formatStateUsing(fn (?string $state): string => $state ? "heroicon-o-{$state}" : '—'),

                Tables\Columns\TextColumn::make('value')
                    ->label('القيمة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('label_ar')
                    ->label('التسمية (عربي)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('label_en')
                    ->label('Label (EN)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('site')
                    ->label('الموقع')
                    ->colors([
                        'primary' => 'waleda',
                        'success' => 'manzuma',
                        'warning' => 'both',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'waleda'  => 'والدة حلم',
                        'manzuma' => 'المنظومة',
                        'both'    => 'كلاهما',
                    }),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('site')
                    ->label('الموقع')
                    ->options([
                        'waleda'  => 'والدة حلم',
                        'manzuma' => 'المنظومة المجتمعية',
                        'both'    => 'كلاهما',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (HeroStatistic $record): string => $record->is_active ? 'إخفاء' : 'تفعيل')
                    ->icon(fn (HeroStatistic $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(fn (HeroStatistic $record) => $record->update(['is_active' => ! $record->is_active])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHeroStatistics::route('/'),
            'create' => Pages\CreateHeroStatistic::route('/create'),
            'edit'   => Pages\EditHeroStatistic::route('/{record}/edit'),
        ];
    }
}