<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessLibraryResource\Pages;
use App\Models\BusinessLibrary;
use App\Models\BusinessLibraryCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BusinessLibraryResource extends Resource
{
    protected static ?string $model = BusinessLibrary::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'المكتبة التجارية';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'مورد';

    protected static ?string $pluralModelLabel = 'موارد المكتبة';

    protected static int $globalSearchResultsLimit = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المورد')
                    ->description('أدخل تفاصيل الملف أو المورد.')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255)
                            ->minLength(3)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) =>
                                $set('slug', Str::slug($state))
                            )
                            ->placeholder('أدخل عنوان المورد')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر')
                            ->required()
                            ->maxLength(255)
                            ->unique(BusinessLibrary::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->placeholder('يُولَّد تلقائياً من العنوان')
                            ->helperText('يجب أن يكون فريداً. أحرف وأرقام وشرطات فقط.')
                            ->columnSpan(1),

                        Forms\Components\Select::make('business_library_category_id')
                            ->label('التصنيف')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم التصنيف')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) =>
                                        $set('slug', Str::slug($state))
                                    ),
                                Forms\Components\TextInput::make('slug')
                                    ->label('الرابط المختصر')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(BusinessLibraryCategory::class, 'slug'),
                                Forms\Components\Textarea::make('description')
                                    ->label('الوصف')
                                    ->maxLength(1000)
                                    ->rows(3),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->maxLength(1000)
                            ->rows(4)
                            ->placeholder('وصف اختياري للمورد...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('الملف')
                    ->description('ارفع الملف الخاص بهذا المورد.')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('الملف')
                            ->required()
                            ->disk('public')
                            ->directory('business-library/files')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                'application/zip',
                                'text/plain',
                            ])
                            ->maxSize(20480)
                            ->downloadable()
                            ->previewable(false)
                            ->helperText('الحد الأقصى 20MB — PDF، Word، Excel، PowerPoint، ZIP، TXT.')
                            ->columnSpanFull(),
                    ]),
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

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->copyable()
                    ->copyMessage('تم نسخ العنوان!'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->toggleable()
                    ->placeholder('لا يوجد وصف'),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('الملف')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—')
                    ->icon('heroicon-o-paper-clip')
                    ->color('primary')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->file_path ? basename($record->file_path) : null)
                    ->toggleable(),

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
                Tables\Filters\SelectFilter::make('business_library_category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('has_file')
                    ->label('يحتوي على ملف')
                    ->query(fn (Builder $query) => $query->whereNotNull('file_path')->where('file_path', '!=', ''))
                    ->toggle(),

                Tables\Filters\Filter::make('no_file')
                    ->label('بدون ملف')
                    ->query(fn (Builder $query) => $query->whereNull('file_path')->orWhere('file_path', ''))
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
                Tables\Actions\Action::make('download')
                    ->label('تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (BusinessLibrary $record) => $record->file_path
                        ? asset('storage/' . $record->file_path)
                        : null
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (BusinessLibrary $record) => (bool) $record->file_path),

                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('حذف المورد')
                    ->modalDescription('هل أنت متأكد من حذف هذا المورد؟ لا يمكن التراجع عن هذا الإجراء.')
                    ->modalSubmitActionLabel('نعم، احذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف الموارد المحددة')
                        ->modalDescription('هل أنت متأكد من حذف الموارد المحددة؟ لا يمكن التراجع عن هذا الإجراء.'),
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
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateHeading('لا توجد موارد بعد')
            ->emptyStateDescription('أضف أول مورد للمكتبة التجارية.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('إضافة مورد')
                    ->url(route('filament.admin.resources.business-libraries.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'التصنيف' => $record->category?->name,
            'الملف'   => $record->file_path ? basename($record->file_path) : 'لا يوجد',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('category');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBusinessLibraries::route('/'),
            'create' => Pages\CreateBusinessLibrary::route('/create'),
            'edit'   => Pages\EditBusinessLibrary::route('/{record}/edit'),
        ];
    }
}