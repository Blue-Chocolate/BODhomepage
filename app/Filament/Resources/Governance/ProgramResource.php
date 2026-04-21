<?php

namespace App\Filament\Resources\Governance;

use App\Filament\Resources\Governance\ProgramResource\Pages;
use App\Models\Governance\Program;
use App\Services\Governance\GovernanceScoringService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ProgramResource extends Resource
{
    protected static ?string $model            = Program::class;
    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = 'الحوكمة والأثر';
    protected static ?string $navigationLabel  = 'البرامج والمشاريع';
    protected static ?string $modelLabel       = 'برنامج';
    protected static ?string $pluralModelLabel = 'البرامج والمشاريع';
    protected static ?int    $navigationSort   = 1;

    // ─── Form ─────────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات البرنامج')->schema([
                Forms\Components\Select::make('organization_id')
                    ->label('الجهة')
                    ->relationship('organization', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('اسم البرنامج')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label('حالة المشروع')
                    ->options([
                        'planning'    => 'في مرحلة التخطيط',
                        'in_progress' => 'جار التنفيذ',
                        'completed'   => 'مكتمل',
                        'suspended'   => 'متوقف',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('execution_duration')
                    ->label('مدة التنفيذ (يوم)')
                    ->numeric()
                    ->minValue(1),

                Forms\Components\TextInput::make('total_actual_cost')
                    ->label('التكلفة الفعلية الإجمالية (ريال)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('resource_efficiency')
                    ->label('كفاءة الموارد (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('الجهة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم البرنامج')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Program::$statusLabels[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'planning'    => 'info',
                        'suspended'   => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('governanceScore.overall_score')
                    ->label('مؤشر الحوكمة')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . ' / 100' : '—')
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'info',
                        $state >= 40 => 'warning',
                        default      => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('quarters_count')
                    ->label('الأرباع')
                    ->counts('quarters'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'planning'    => 'في مرحلة التخطيط',
                        'in_progress' => 'جار التنفيذ',
                        'completed'   => 'مكتمل',
                        'suspended'   => 'متوقف',
                    ]),

                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('الجهة')
                    ->relationship('organization', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('recalculate')
                    ->label('إعادة الحساب')
                    ->icon('heroicon-o-calculator')
                    ->action(function (Program $record) {
                        $year = $record->quarters()->max('year') ?? date('Y');
                        app(GovernanceScoringService::class)->calculate($record, $year);
                        Notification::make()->title('تم إعادة حساب مؤشر الحوكمة')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // ─── Infolist ─────────────────────────────────────────────────────────────────

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('ملخص البرنامج')->schema([
                Infolists\Components\TextEntry::make('organization.name')->label('الجهة'),
                Infolists\Components\TextEntry::make('name')->label('اسم البرنامج'),
                Infolists\Components\TextEntry::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Program::$statusLabels[$state] ?? $state),
                Infolists\Components\TextEntry::make('total_actual_cost')
                    ->label('التكلفة الفعلية')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' ريال'),
                Infolists\Components\TextEntry::make('resource_efficiency')
                    ->label('كفاءة الموارد')
                    ->formatStateUsing(fn ($state) => $state . '%'),
                Infolists\Components\TextEntry::make('execution_duration')
                    ->label('مدة التنفيذ')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' يوم' : '—'),
            ])->columns(3),

            Infolists\Components\Section::make('مؤشر الحوكمة')->schema([
                Infolists\Components\TextEntry::make('governanceScore.overall_score')
                    ->label('النتيجة الإجمالية')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . ' / 100' : 'لم يحسب بعد'),
                Infolists\Components\TextEntry::make('governanceScore.classification.label')
                    ->label('التصنيف'),
                Infolists\Components\TextEntry::make('governanceScore.total_beneficiaries')
                    ->label('إجمالي المستفيدين'),
                Infolists\Components\TextEntry::make('governanceScore.budget_variance')
                    ->label('انحراف الميزانية')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '—'),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit'   => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}