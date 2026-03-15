<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use App\Models\BlogCateogry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'إدارة المدونة';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    protected static int $globalSearchResultsLimit = 10;

    protected static ?string $modelLabel = 'مقال';

    protected static ?string $pluralModelLabel = 'المقالات';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('is_published', true)->count() > 0 ? 'success' : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المقال')
                    ->description('أدخل تفاصيل المقال بالكامل.')
                    ->icon('heroicon-o-document-text')
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
                            ->placeholder('أدخل عنوان المقال')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر')
                            ->required()
                            ->maxLength(255)
                            ->unique(Blog::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->placeholder('يُولَّد تلقائياً من العنوان')
                            ->helperText('يجب أن يكون فريداً. أحرف وأرقام وشرطات فقط.')
                            ->columnSpan(1),

                        Forms\Components\Select::make('blog_category_id')
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
                                    ->unique(BlogCateogry::class, 'slug'),
                                Forms\Components\Textarea::make('description')
                                    ->label('الوصف')
                                    ->maxLength(1000)
                                    ->rows(3),
                            ])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('author')
                            ->label('الكاتب')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('اسم الكاتب')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('محتوى المقال')
                    ->description('أدخل وصفاً مختصراً والمحتوى الكامل للمقال.')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('الوصف المختصر')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('ملخص قصير يظهر في قوائم المقالات...')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('المحتوى الكامل')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blogs/attachments')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('الصورة والنشر')
                    ->description('إعدادات الصورة الرئيسية وحالة النشر.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('الصورة الرئيسية')
                            ->image()
                            ->disk('public')
                            ->directory('blogs/images')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('الحد الأقصى 2MB — JPEG، PNG، WebP فقط.')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_published')
                            ->label('منشور؟')
                            ->default(false)
                            ->live()
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('تاريخ النشر')
                            ->nullable()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->placeholder('اختر تاريخ ووقت النشر')
                            ->helperText('اتركه فارغاً للنشر الفوري عند التفعيل.')
                            ->visible(fn (Forms\Get $get) => $get('is_published'))
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.png'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->copyable()
                    ->copyMessage('تم نسخ العنوان!'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('author')
                    ->label('الكاتب')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('short_description')
                    ->label('الوصف المختصر')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->short_description)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('لا يوجد وصف'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('منشور')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->placeholder('غير محدد')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('blog_category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('حالة النشر')
                    ->trueLabel('منشور فقط')
                    ->falseLabel('غير منشور فقط')
                    ->native(false),

                Tables\Filters\Filter::make('published_at')
                    ->label('له تاريخ نشر')
                    ->query(fn (Builder $query) => $query->whereNotNull('published_at'))
                    ->toggle(),

                Tables\Filters\Filter::make('no_image')
                    ->label('بدون صورة')
                    ->query(fn (Builder $query) => $query->whereNull('image_path')->orWhere('image_path', ''))
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
                Tables\Actions\Action::make('toggle_publish')
                    ->label(fn (Blog $record) => $record->is_published ? 'إلغاء النشر' : 'نشر')
                    ->icon(fn (Blog $record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Blog $record) => $record->is_published ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Blog $record) => $record->is_published ? 'إلغاء نشر المقال' : 'نشر المقال')
                    ->modalDescription(fn (Blog $record) => $record->is_published ? 'هل أنت متأكد من إلغاء نشر هذا المقال؟' : 'هل أنت متأكد من نشر هذا المقال؟')
                    ->action(fn (Blog $record) => $record->update(['is_published' => ! $record->is_published])),

                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('حذف المقال')
                    ->modalDescription('هل أنت متأكد من حذف هذا المقال؟ لا يمكن التراجع عن هذا الإجراء.')
                    ->modalSubmitActionLabel('نعم، احذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('نشر المحدد')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('نشر المقالات المحددة')
                        ->modalDescription('هل أنت متأكد من نشر المقالات المحددة؟')
                        ->action(fn ($records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('unpublish_selected')
                        ->label('إلغاء نشر المحدد')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('إلغاء نشر المقالات المحددة')
                        ->modalDescription('هل أنت متأكد من إلغاء نشر المقالات المحددة؟')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف المقالات المحددة')
                        ->modalDescription('هل أنت متأكد من حذف المقالات المحددة؟ لا يمكن التراجع عن هذا الإجراء.'),
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
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('لا توجد مقالات بعد')
            ->emptyStateDescription('أنشئ أول مقال للبدء.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('إنشاء مقال')
                    ->url(route('filament.admin.resources.blogs.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'author', 'short_description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'التصنيف' => $record->category?->name,
            'الكاتب'  => $record->author,
            'الحالة'  => $record->is_published ? 'منشور' : 'غير منشور',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('category');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit'   => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}