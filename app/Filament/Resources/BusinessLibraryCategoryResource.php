<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessLibraryCategoryResource\Pages;
use App\Models\BusinessLibraryCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BusinessLibraryCategoryResource extends Resource
{
    protected static ?string $model = BusinessLibraryCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationGroup = 'المكتبة التجارية';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'تصنيف';

    protected static ?string $pluralModelLabel = 'تصنيفات المكتبة';

    protected static int $globalSearchResultsLimit = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات التصنيف')
                    ->description('أدخل تفاصيل تصنيف المكتبة.')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم التصنيف')
                            ->required()
                            ->maxLength(255)
                            ->minLength(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) =>
                                $set('slug', Str::slug($state))
                            )
                            ->placeholder('أدخل اسم التصنيف')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر')
                            ->required()
                            ->maxLength(255)
                            ->unique(BusinessLibraryCategory::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->placeholder('يُولَّد تلقائياً من الاسم')
                            ->helperText('يجب أن يكون فريداً. أحرف وأرقام وشرطات فقط.')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->maxLength(1000)
                            ->rows(4)
                            ->placeholder('وصف اختياري للتصنيف...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('تم النسخ!'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('تم نسخ الرابط!'),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->description)
                    ->toggleable()
                    ->placeholder('لا يوجد وصف'),

                Tables\Columns\TextColumn::make('resources_count')
                    ->label('الملفات')
                    ->counts('resources')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_resources')
                    ->label('يحتوي على ملفات')
                    ->query(fn (Builder $query) => $query->has('resources'))
                    ->toggle(),

                Tables\Filters\Filter::make('no_resources')
                    ->label('تصنيفات فارغة')
                    ->query(fn (Builder $query) => $query->doesntHave('resources'))
                    ->toggle(),

                Tables\Filters\Filter::make('created_today')
                    ->label('أُنشئ اليوم')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('created_this_week')
                    ->label('أُنشئ هذا الأسبوع')
                    ->query(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('حذف التصنيف')
                    ->modalDescription('هل أنت متأكد من حذف هذا التصنيف؟ لا يمكن التراجع عن هذا الإجراء.')
                    ->modalSubmitActionLabel('نعم، احذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف التصنيفات المحددة')
                        ->modalDescription('هل أنت متأكد من حذف التصنيفات المحددة؟ لا يمكن التراجع عن هذا الإجراء.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->persistColumnSearchesInSession()
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateHeading('لا توجد تصنيفات بعد')
            ->emptyStateDescription('أنشئ أول تصنيف للمكتبة التجارية.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('إنشاء تصنيف')
                    ->url(route('filament.admin.resources.business-library-categories.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'الرابط' => $record->slug,
            'الملفات' => $record->resources_count ?? 0,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->withCount('resources');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBusinessLibraryCategories::route('/'),
            'create' => Pages\CreateBusinessLibraryCategory::route('/create'),
            'edit'   => Pages\EditBusinessLibraryCategory::route('/{record}/edit'),
        ];
    }
}