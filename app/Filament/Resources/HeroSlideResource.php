<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon        = 'heroicon-o-photo';
    protected static ?string $navigationGroup       = 'إدارة المنظومة الاجتماعية';
    protected static ?string $navigationLabel       = 'شرائح الهيرو';
    protected static ?string $modelLabel            = 'شريحة هيرو';
    protected static ?string $pluralModelLabel      = 'شرائح الهيرو';
    protected static ?int    $navigationSort        = 10;

    // ─── FORM ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Tabs::make('Tabs')->tabs([

                // ── Tab 1: Content ────────────────────────────────────────────
                Tabs\Tab::make('المحتوى')
                    ->icon('heroicon-o-language')
                    ->schema([

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

                        Section::make('العنوان والنص الفرعي')->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('title_ar')
                                    ->label('العنوان الرئيسي (عربي) *')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('title_en')
                                    ->label('Main Title (English) *')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            Forms\Components\FileUpload::make('logo_url')
    ->label('Logo')
    ->image()
    ->directory('logos')
    ->nullable(),
                            Grid::make(2)->schema([
                                Forms\Components\Textarea::make('subtitle_ar')
                                    ->label('النص الفرعي (عربي)')
                                    ->rows(2),
                                Forms\Components\Textarea::make('subtitle_en')
                                    ->label('Subtitle (English)')
                                    ->rows(2),
                            ]),
                        ]),

                        Section::make('أزرار الحث على الإجراء (CTA)')->schema([

                            // Primary CTA
                            Forms\Components\Fieldset::make('الزر الأول (Primary)')->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('cta_primary_text_ar')
                                        ->label('النص (عربي)'),
                                    Forms\Components\TextInput::make('cta_primary_text_en')
                                        ->label('Text (English)'),
                                    Forms\Components\Select::make('cta_primary_style')
                                        ->label('النمط')
                                        ->options([
                                            'solid'   => 'مصمت',
                                            'outline' => 'بدون تعبئة',
                                            'ghost'   => 'شفاف',
                                        ])
                                        ->default('solid')
                                        ->native(false),
                                ]),
                                Forms\Components\TextInput::make('cta_primary_url')
                                    ->label('الرابط')
                                    ->url()
                                    ->columnSpanFull(),
                            ]),

                            // Secondary CTA
                            Forms\Components\Fieldset::make('الزر الثاني (Secondary)')->schema([
                                Forms\Components\Toggle::make('cta_secondary_visible')
                                    ->label('إظهار الزر الثاني')
                                    ->default(true)
                                    ->inline(false),
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('cta_secondary_text_ar')
                                        ->label('النص (عربي)'),
                                    Forms\Components\TextInput::make('cta_secondary_text_en')
                                        ->label('Text (English)'),
                                ]),
                                Forms\Components\TextInput::make('cta_secondary_url')
                                    ->label('الرابط')
                                    ->url()
                                    ->columnSpanFull(),
                            ]),
                        ]),
                    ]),

                // ── Tab 2: Media ──────────────────────────────────────────────
                Tabs\Tab::make('الوسائط والمظهر')
                    ->icon('heroicon-o-film')
                    ->schema([

                        Section::make('صور الخلفية')->schema([
                            Grid::make(2)->schema([
                                Forms\Components\FileUpload::make('background_image')
                                    ->label('صورة الخلفية — سطح المكتب')
                                    ->image()
                                    ->directory('hero/desktop')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['16:9'])
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(10240)
                                    ->helperText('الحد الأدنى الموصى به: 1920×1080 بكسل'),

                                Forms\Components\FileUpload::make('background_image_mobile')
                                    ->label('صورة الخلفية — الجوال (Portrait)')
                                    ->image()
                                    ->directory('hero/mobile')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['9:16'])
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(10240)
                                    ->helperText('يُستخدم على شاشات الجوال — يُرث من صورة سطح المكتب إن ترك فارغاً'),
                            ]),

                            Forms\Components\TextInput::make('background_video_url')
                                ->label('رابط فيديو الخلفية (اختياري)')
                                ->url()
                                ->helperText('يدعم YouTube أو Vimeo أو رابط .mp4 مباشر')
                                ->prefix('https://')
                                ->columnSpanFull(),
                        ]),

                        Section::make('طبقة التعتيم (Overlay)')->schema([
                            Grid::make(2)->schema([
                                Forms\Components\ColorPicker::make('overlay_color')
                                    ->label('لون التعتيم')
                                    ->default('#000000'),

                                Forms\Components\TextInput::make('overlay_opacity')
                                    ->label('درجة الشفافية')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(40)
                                    ->suffix('%')
                                    ->helperText('0 = شفاف تماماً — 100 = معتم تماماً'),
                            ]),
                        ]),

                        Section::make('تأثير الانتقال والتوقيت')->schema([
                            Grid::make(2)->schema([
                                Forms\Components\Select::make('transition_effect')
                                    ->label('تأثير الانتقال')
                                    ->options([
                                        'fade'      => 'تلاشي (Fade)',
                                        'slide'     => 'انزلاق (Slide)',
                                        'zoom'      => 'تكبير (Zoom)',
                                        'ken_burns' => 'Ken Burns',
                                    ])
                                    ->default('fade')
                                    ->native(false),

                                Forms\Components\TextInput::make('display_duration')
                                    ->label('مدة عرض الشريحة')
                                    ->numeric()
                                    ->minValue(2)
                                    ->maxValue(30)
                                    ->default(5)
                                    ->suffix('ثانية'),
                            ]),
                        ]),
                    ]),

                // ── Tab 3: Slider Controls ────────────────────────────────────
                Tabs\Tab::make('إعدادات التحكم')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([

                        Section::make('خيارات Slider')->schema([
                            Grid::make(2)->schema([
                                Forms\Components\Toggle::make('autoplay')
                                    ->label('تشغيل تلقائي')
                                    ->default(true)
                                    ->inline(false),

                                Forms\Components\Toggle::make('loop')
                                    ->label('تكرار لا نهائي')
                                    ->default(true)
                                    ->inline(false),

                                Forms\Components\Toggle::make('show_arrows')
                                    ->label('إظهار أسهم التنقل')
                                    ->default(true)
                                    ->inline(false),

                                Forms\Components\Toggle::make('show_dots')
                                    ->label('إظهار نقاط التنقل')
                                    ->default(true)
                                    ->inline(false),
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
                    ]),

            ])->columnSpanFull(),
        ]);
    }

    // ─── TABLE ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('background_image')
                    ->label('الصورة')
                    ->disk('public')
                    ->height(56)
                    ->width(100)
                    ->defaultImageUrl(asset('images/placeholder.png')),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label('العنوان (عربي)')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('site')
                    ->label('الموقع')
                    ->colors([
                        'primary' => 'waleda',
                        'success' => 'manzuma',
                        'warning' => 'both',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'waleda'  => 'ولادة حلم',
                        'manzuma' => 'المنظومة',
                        'both'    => 'كلاهما',
                    }),

                Tables\Columns\BadgeColumn::make('transition_effect')
                    ->label('التأثير')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fade'      => 'Fade',
                        'slide'     => 'Slide',
                        'zoom'      => 'Zoom',
                        'ken_burns' => 'Ken Burns',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('display_duration')
                    ->label('المدة')
                    ->suffix('ث')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('autoplay')
                    ->label('تلقائي')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')       // drag-and-drop reorder
            ->filters([
                Tables\Filters\SelectFilter::make('site')
                    ->label('الموقع')
                    ->options([
                        'waleda'  => 'والدة حلم',
                        'manzuma' => 'المنظومة المجتمعية',
                        'both'    => 'كلاهما',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->trueLabel('نشطة فقط')
                    ->falseLabel('مخفية فقط'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (HeroSlide $record): string => $record->is_active ? 'إخفاء' : 'تفعيل')
                    ->icon(fn (HeroSlide $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (HeroSlide $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(fn (HeroSlide $record) => $record->update(['is_active' => ! $record->is_active])),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('إخفاء المحدد')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }

    // ─── PAGES ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit'   => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}