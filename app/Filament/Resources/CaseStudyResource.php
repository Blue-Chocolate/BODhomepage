<?php
// app/Filament/Resources/CaseStudyResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CaseStudyResource\Pages;
use App\Models\CaseStudy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'دراسات الحالة';
    protected static ?string $modelLabel = 'دراسة حالة';
    protected static ?string $pluralModelLabel = 'دراسات الحالة';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المعلومات الأساسية')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('الرابط المختصر')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'publish' => 'منشور',
                            'draft'   => 'مسودة',
                            'private' => 'خاص',
                        ])
                        ->default('publish')
                        ->required()
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'publish' => 'success',
                            'draft'   => 'warning',
                            'private' => 'gray',
                        }),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('تاريخ النشر'),
                ])
                ->columns(2),

            Forms\Components\Section::make('المحتوى')
                ->schema([
                    Forms\Components\Textarea::make('excerpt')
                        ->label('المقتطف')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('content_text')
                        ->label('المحتوى')
                        ->rows(10)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('الصورة')
                ->schema([
                    Forms\Components\TextInput::make('image_url')
                        ->label('رابط الصورة')
                        ->url()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('image_drive_file_id')
                        ->label('معرف ملف Drive'),

                    Forms\Components\TextInput::make('image_drive_link')
                        ->label('رابط Drive')
                        ->url(),

                    Forms\Components\TextInput::make('image_file_name')
                        ->label('اسم الملف'),

                    Forms\Components\Select::make('image_upload_status')
                        ->label('حالة الرفع')
                        ->options([
                            'uploaded'    => 'تم الرفع',
                            'pending'     => 'في الانتظار',
                            'failed'      => 'فشل',
                        ])
                        ->default('uploaded'),
                ])
                ->columns(2),

            Forms\Components\Section::make('معلومات إضافية')
                ->schema([
                    Forms\Components\TextInput::make('author_name')
                        ->label('الكاتب'),

                    Forms\Components\TextInput::make('author_id')
                        ->label('معرف الكاتب')
                        ->numeric(),

                    Forms\Components\TextInput::make('category_id')
                        ->label('معرف التصنيف')
                        ->numeric(),

                    Forms\Components\TextInput::make('tags')
                        ->label('الوسوم'),

                    Forms\Components\TextInput::make('reading_time')
                        ->label('وقت القراءة'),

                    Forms\Components\TextInput::make('link')
                        ->label('الرابط الخارجي')
                        ->url(),

                    Forms\Components\TextInput::make('post_id')
                        ->label('معرف المنشور الأصلي')
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
Tables\Columns\TextColumn::make('status')
    ->label('الحالة')
    ->badge()
    ->formatStateUsing(fn ($state) => match ($state) {
        'publish' => 'منشور',
        'draft'   => 'مسودة',
        'private' => 'خاص',
        default   => $state,
    })
    ->color(fn ($state) => match ($state) {
        'publish' => 'success',
        'draft'   => 'warning',
        'private' => 'gray',
        default   => 'secondary',
    }),

                Tables\Columns\TextColumn::make('author_name')
                    ->label('الكاتب')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reading_time')
                    ->label('وقت القراءة'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
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
                        'private' => 'خاص',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->label('المحذوفات'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
                Tables\Actions\RestoreAction::make()->label('استعادة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('حذف نهائي'),
                    Tables\Actions\RestoreBulkAction::make()->label('استعادة المحدد'),
                ]),
            ])
            ->defaultSort('published_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit'   => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}