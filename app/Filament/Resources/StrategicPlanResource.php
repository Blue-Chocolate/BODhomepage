<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrategicPlanResource\Pages;
use App\Models\StrategicPlan;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StrategicPlanResource extends Resource
{
    protected static ?string $model = StrategicPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'الخطط الاستراتيجية';

    protected static ?string $modelLabel = 'خطة استراتيجية';

    protected static ?string $pluralModelLabel = 'الخطط الاستراتيجية';

    protected static ?int $navigationSort = 1;

    // ─── Form ────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('المعلومات الأساسية')
                ->schema([
                    TextInput::make('post_id')
                        ->label('Post ID')
                        ->numeric()
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Textarea::make('excerpt')
                        ->label('المقتطف')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('content_text')
                        ->label('المحتوى')
                        ->rows(6)
                        ->columnSpanFull(),

                    Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'publish' => 'منشور',
                            'draft'   => 'مسودة',
                            'private' => 'خاص',
                            'trash'   => 'محذوف',
                        ])
                        ->default('publish')
                        ->required(),

                    TextInput::make('categories')
                        ->label('التصنيف')
                        ->numeric(),

                    DateTimePicker::make('post_date')
                        ->label('تاريخ النشر'),

                    DateTimePicker::make('post_modified')
                        ->label('تاريخ التعديل'),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->maxLength(500)
                        ->columnSpanFull(),

                    TextInput::make('link')
                        ->label('رابط المنشور')
                        ->url()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('الروابط الخارجية')
                ->schema([
                    TextInput::make('execution_report')
                        ->label('رابط تقرير التنفيذ')
                        ->url()
                        ->columnSpanFull(),

                    TextInput::make('association_website')
                        ->label('موقع الجمعية')
                        ->url()
                        ->columnSpanFull(),
                ]),

            Section::make('الصور')
                ->schema([
                    TextInput::make('image_url')
                        ->label('الصورة البارزة (URL)')
                        ->url()
                        ->columnSpanFull(),

                    TextInput::make('content_image_1')
                        ->label('صورة المحتوى 1 (URL)')
                        ->url()
                        ->columnSpanFull(),

                    TextInput::make('content_image_2')
                        ->label('صورة المحتوى 2 (URL)')
                        ->url()
                        ->columnSpanFull(),
                ]),

            Section::make('Google Drive — الصورة البارزة')
                ->collapsed()
                ->schema([
                    TextInput::make('image_drive_file_id')->label('File ID'),
                    TextInput::make('image_drive_link')->label('Drive Link')->url(),
                    TextInput::make('image_file_name')->label('اسم الملف'),
                    Select::make('image_upload_status')
                        ->label('حالة الرفع')
                        ->options(['uploaded' => 'مرفوع', 'pending' => 'معلق', 'failed' => 'فشل']),
                ])
                ->columns(2),

            Section::make('Google Drive — صورة المحتوى 1')
                ->collapsed()
                ->schema([
                    TextInput::make('content_image_1_drive_file_id')->label('File ID'),
                    TextInput::make('content_image_1_drive_link')->label('Drive Link')->url(),
                    TextInput::make('content_image_1_file_name')->label('اسم الملف'),
                    Select::make('content_image_1_upload_status')
                        ->label('حالة الرفع')
                        ->options(['uploaded' => 'مرفوع', 'pending' => 'معلق', 'failed' => 'فشل']),
                ])
                ->columns(2),

            Section::make('Google Drive — صورة المحتوى 2')
                ->collapsed()
                ->schema([
                    TextInput::make('content_image_2_drive_file_id')->label('File ID'),
                    TextInput::make('content_image_2_drive_link')->label('Drive Link')->url(),
                    TextInput::make('content_image_2_file_name')->label('اسم الملف'),
                    Select::make('content_image_2_upload_status')
                        ->label('حالة الرفع')
                        ->options(['uploaded' => 'مرفوع', 'pending' => 'معلق', 'failed' => 'فشل']),
                ])
                ->columns(2),

        ]);
    }

    // ─── Table ───────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->circular(false)
                    ->height(50)
                    ->width(80),

                TextColumn::make('post_id')
                    ->label('Post ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'publish' => 'success',
                        'draft'   => 'warning',
                        'private' => 'info',
                        'trash'   => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('categories')
                    ->label('التصنيف')
                    ->sortable(),

                TextColumn::make('post_date')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d')
                    ->sortable(),

                TextColumn::make('image_upload_status')
                    ->label('رفع الصورة')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'uploaded' => 'success',
                        'pending'  => 'warning',
                        'failed'   => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'publish' => 'منشور',
                        'draft'   => 'مسودة',
                        'private' => 'خاص',
                        'trash'   => 'محذوف',
                    ]),

                SelectFilter::make('image_upload_status')
                    ->label('حالة رفع الصورة')
                    ->options([
                        'uploaded' => 'مرفوع',
                        'pending'  => 'معلق',
                        'failed'   => 'فشل',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('post_date', 'desc');
    }

    // ─── Pages ───────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStrategicPlans::route('/'),
            'create' => Pages\CreateStrategicPlan::route('/create'),
            'edit'   => Pages\EditStrategicPlan::route('/{record}/edit'),
        ];
    }
}