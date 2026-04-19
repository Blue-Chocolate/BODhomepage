<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Models\Compliance\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'الامتثال';
    protected static ?string $navigationLabel = 'نماذج التقييم';
    protected static ?string $modelLabel      = 'نموذج تقييم';
    protected static ?string $pluralModelLabel = 'نماذج التقييم';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات النموذج')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان التقييم')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('period_year')
                    ->label('سنة الفترة')
                    ->required()
                    ->placeholder(date('Y'))
                    ->maxLength(4),

                Forms\Components\DatePicker::make('opens_at')
                    ->label('تاريخ الفتح'),

                Forms\Components\DatePicker::make('closes_at')
                    ->label('تاريخ الإغلاق')
                    ->after('opens_at'),

                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_year')
                    ->label('السنة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('axes_count')
                    ->label('المحاور')
                    ->counts('axes'),

                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('التقييمات')
                    ->counts('submissions'),

                Tables\Columns\TextColumn::make('opens_at')
                    ->label('يفتح')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('closes_at')
                    ->label('يغلق')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('النشاط'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('period_year', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // AssessmentAxesRelationManager (see below)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'edit'   => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }
}