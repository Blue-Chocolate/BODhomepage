<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnualPlanResource\Pages;
use App\Modules\AnnualPlans\Models\AnnualPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnualPlanResource extends Resource
{
    protected static ?string $model = AnnualPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?string $modelLabel = 'خطة سنوية';

    protected static ?string $pluralModelLabel = 'الخطط السنوية';

    protected static ?int $navigationSort = 10;

    // ─── Form ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('المعلومات الأساسية')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('slug')
                        ->label('الرابط المختصر (Slug)')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('post_id')
                        ->label('معرف المنشور (WordPress)')
                        ->numeric()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('category_id')
                        ->label('معرف التصنيف')
                        ->numeric(),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'publish' => 'منشور',
                            'draft'   => 'مسودة',
                            'pending' => 'قيد المراجعة',
                        ])
                        ->default('publish')
                        ->required(),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('تاريخ النشر'),

                    Forms\Components\TextInput::make('link')
                        ->label('رابط المنشور')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('excerpt')
                        ->label('المقتطف')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('content_text')
                        ->label('محتوى النص')
                        ->rows(5)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('الصورة الرئيسية')
                ->schema([
                    Forms\Components\TextInput::make('image_url')
                        ->label('رابط الصورة')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('image_file_name')
                        ->label('اسم الملف')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('image_drive_file_id')
                        ->label('معرف Drive')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('image_drive_link')
                        ->label('رابط Drive')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('image_upload_status')
                        ->label('حالة الرفع')
                        ->options([
                            'pending'  => 'قيد الانتظار',
                            'uploaded' => 'تم الرفع',
                            'failed'   => 'فشل',
                        ])
                        ->default('pending'),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('صورة المحتوى الأولى')
                ->schema([
                    Forms\Components\TextInput::make('content_image_1_url')
                        ->label('رابط الصورة')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('content_image_1_file_name')
                        ->label('اسم الملف')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('content_image_1_drive_file_id')
                        ->label('معرف Drive')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('content_image_1_drive_link')
                        ->label('رابط Drive')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('content_image_1_upload_status')
                        ->label('حالة الرفع')
                        ->options([
                            'pending'  => 'قيد الانتظار',
                            'uploaded' => 'تم الرفع',
                            'failed'   => 'فشل',
                        ])
                        ->default('pending'),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->square()
                    ->size(48),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(60),

                Tables\Columns\TextColumn::make('post_id')
                    ->label('Post ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category_id')
                    ->label('التصنيف')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'publish',
                        'warning' => 'pending',
                        'danger'  => 'draft',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'publish' => 'منشور',
                        'draft'   => 'مسودة',
                        'pending' => 'قيد المراجعة',
                        default   => $state,
                    }),

                Tables\Columns\BadgeColumn::make('image_upload_status')
                    ->label('حالة الصورة')
                    ->colors([
                        'success' => 'uploaded',
                        'warning' => 'pending',
                        'danger'  => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('أُضيف')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'publish' => 'منشور',
                        'draft'   => 'مسودة',
                        'pending' => 'قيد المراجعة',
                    ]),

                Tables\Filters\SelectFilter::make('image_upload_status')
                    ->label('حالة الصورة')
                    ->options([
                        'pending'  => 'قيد الانتظار',
                        'uploaded' => 'تم الرفع',
                        'failed'   => 'فشل',
                    ]),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('حذف نهائي'),
                    Tables\Actions\RestoreBulkAction::make()->label('استعادة'),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [];
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnualPlans::route('/'),
            'create' => Pages\CreateAnnualPlan::route('/create'),
            'edit'   => Pages\EditAnnualPlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}