<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Compliance\AssessmentSubmission;
use App\Services\Compliance\ComplianceSubmissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;

class SubmissionResource extends Resource
{
    protected static ?string $model             = AssessmentSubmission::class;
    protected static ?string $navigationIcon    = 'heroicon-o-document-check';
    protected static ?string $navigationGroup   = 'الامتثال';
    protected static ?string $navigationLabel   = 'التقييمات المقدمة';
    protected static ?string $modelLabel        = 'تقييم';
    protected static ?string $pluralModelLabel  = 'التقييمات';
    protected static ?int    $navigationSort    = 2;

    // ─── Table ───────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('الجهة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.type')
                    ->label('النوع')
                    ->badge(),

                Tables\Columns\TextColumn::make('assessment.period_year')
                    ->label('السنة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'reviewed'  => 'info',
                        'approved'  => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft'     => 'مسودة',
                        'submitted' => 'مقدم',
                        'reviewed'  => 'تمت المراجعة',
                        'approved'  => 'معتمد',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('overall_score')
                    ->label('النتيجة الإجمالية')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . ' / 5' : '—')
                    ->color(fn ($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'success',
                        $state >= 2.5 => 'warning',
                        $state >= 1.5 => 'danger',
                        default       => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('تاريخ التقديم')
                    ->dateTime('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('المقيّم')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft'     => 'مسودة',
                        'submitted' => 'مقدم',
                        'reviewed'  => 'تمت المراجعة',
                        'approved'  => 'معتمد',
                    ]),

                Tables\Filters\SelectFilter::make('assessment_id')
                    ->label('نموذج التقييم')
                    ->relationship('assessment', 'title'),

                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('الجهة')
                    ->relationship('organization', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // ─── Form ─────────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات التقييم')->schema([
                Forms\Components\Select::make('assessment_id')
                    ->label('نموذج التقييم')
                    ->relationship('assessment', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('organization_id')
                    ->label('الجهة')
                    ->relationship('organization', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('evaluated_by')
                    ->label('المقيّم')
                    ->relationship('evaluator', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft'     => 'مسودة',
                        'submitted' => 'مقدم',
                        'reviewed'  => 'تمت المراجعة',
                        'approved'  => 'معتمد',
                    ])
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('ملاحظات المقيّم')->schema([
                Forms\Components\Textarea::make('evaluator_notes')
                    ->label('ملاحظات المقيّم')
                    ->rows(4)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('قرار الإدارة (القسم 6)')->schema([
                Forms\Components\Select::make('management_action')
                    ->label('قرار الإدارة')
                    ->options([
                        'approved'            => 'اعتماد الوضع الحالي',
                        'approved_with_plan'  => 'اعتماد مع خطة تحسين',
                        'urgent_treatment'    => 'تحويل للمعالجة العاجلة',
                        'reassess'            => 'إعادة تقييم',
                    ])
                    ->live(),

                Forms\Components\TextInput::make('reassess_months')
                    ->label('إعادة التقييم خلال (أشهر)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(24)
                    ->visible(fn (Forms\Get $get) => $get('management_action') === 'reassess'),

                Forms\Components\Textarea::make('management_decision')
                    ->label('نص قرار الإدارة')
                    ->rows(4)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    // ─── Infolist (View page) ─────────────────────────────────────────────────────

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('ملخص التقييم')->schema([
                Infolists\Components\TextEntry::make('organization.name')->label('الجهة'),
                Infolists\Components\TextEntry::make('organization.type')->label('نوع الجهة')->badge(),
                Infolists\Components\TextEntry::make('assessment.title')->label('نموذج التقييم'),
                Infolists\Components\TextEntry::make('assessment.period_year')->label('السنة'),
                Infolists\Components\TextEntry::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'reviewed'  => 'info',
                        'approved'  => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft'     => 'مسودة',
                        'submitted' => 'مقدم',
                        'reviewed'  => 'تمت المراجعة',
                        'approved'  => 'معتمد',
                        default     => $state,
                    }),
                Infolists\Components\TextEntry::make('overall_score')
                    ->label('النتيجة الإجمالية')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . ' / 5' : 'لم يحسب بعد')
                    ->color(fn ($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'success',
                        $state >= 2.5 => 'warning',
                        default       => 'danger',
                    }),
                Infolists\Components\TextEntry::make('compliance_level.label')
                    ->label('مستوى الامتثال'),
            ])->columns(3),

            Infolists\Components\Section::make('ملاحظات')->schema([
                Infolists\Components\TextEntry::make('evaluator_notes')
                    ->label('ملاحظات المقيّم')
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('management_decision')
                    ->label('قرار الإدارة')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'edit'   => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }
}