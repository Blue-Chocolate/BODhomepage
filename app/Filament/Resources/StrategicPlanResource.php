<?php
// app/Filament/Resources/StrategicPlanResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\StrategicPlanResource\Pages;
use App\Models\StrategicPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StrategicPlanResource extends Resource
{
    protected static ?string $model = StrategicPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'الخطط الاستراتيجية';
    protected static ?string $modelLabel = 'خطة استراتيجية';
    protected static ?string $pluralModelLabel = 'الخطط الاستراتيجية';
    protected static ?string $navigationGroup = 'المحتوى';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('المعلومات الأساسية')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        )
                        ->columnSpanFull(),

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
                        ->required(),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('تاريخ النشر')
                        ->default(now()),

                    Forms\Components\TextInput::make('category_id')
                        ->label('التصنيف')
                        ->numeric()
                        ->nullable(),
                ]),

            Forms\Components\Section::make('المحتوى')
                ->schema([
                    Forms\Components\Textarea::make('excerpt')
                        ->label('المقتطف')
                        ->rows(3)
                        ->nullable(),

                    Forms\Components\Textarea::make('content_text')
                        ->label('المحتوى')
                        ->rows(8)
                        ->nullable(),
                ]),

            Forms\Components\Section::make('الصور')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('image_url')
                        ->label('رابط الصورة الرئيسية')
                        ->url()
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_1')
                        ->label('رابط صورة المحتوى الأولى')
                        ->url()
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_2')
                        ->label('رابط صورة المحتوى الثانية')
                        ->url()
                        ->nullable(),
                ]),

            Forms\Components\Section::make('بيانات Google Drive')
                ->collapsed()
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('image_drive_file_id')
                        ->label('معرّف الصورة الرئيسية')
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_1_drive_file_id')
                        ->label('معرّف الصورة الأولى')
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_2_drive_file_id')
                        ->label('معرّف الصورة الثانية')
                        ->nullable(),

                    Forms\Components\TextInput::make('post_id')
                        ->label('معرّف المنشور الأصلي (WordPress)')
                        ->numeric()
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->circular(false)
                    ->width(60)
                    ->height(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => match($state) {
                        'publish' => 'منشور',
                        'draft'   => 'مسودة',
                        'private' => 'خاص',
                        default   => $state,
                    })
                    ->colors([
                        'success' => 'publish',
                        'warning' => 'draft',
                        'danger'  => 'private',
                    ]),

                Tables\Columns\TextColumn::make('category_id')
                    ->label('التصنيف')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('post_id')
                    ->label('معرّف WP')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStrategicPlans::route('/'),
            'create' => Pages\CreateStrategicPlan::route('/create'),
            'edit'   => Pages\EditStrategicPlan::route('/{record}/edit'),
        ];
    }
}