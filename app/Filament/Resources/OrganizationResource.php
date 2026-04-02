<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization\Organization;
use Filament\Forms\Form;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Organizations';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Information')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('liscense_number')
                    ->label('License Number')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->nullable(),

                TextInput::make('phone')
                    ->tel()
                    ->nullable(),

                TextInput::make('representative_name')
                    ->nullable(),
            ])->columns(2),

            Section::make('Evaluation Details')->schema([
                DateTimePicker::make('evaluation_date')
                    ->nullable(),

                TextInput::make('evaluation_duration')
                    ->label('Duration (days)')
                    ->integer()
                    ->minValue(0)
                    ->nullable(),

                TextInput::make('evaluation_score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->nullable(),

                TextInput::make('evaluator_name')
                    ->nullable(),

                TextInput::make('evaluation_team')
                    ->nullable(),
            ])->columns(2),

            Section::make('Approval')->schema([
                Select::make('approval_status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),

                DateTimePicker::make('approved_at')
                    ->nullable()
                    ->disabled(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('liscense_number')
                    ->label('License No.')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('representative_name')
                    ->toggleable(),

                TextColumn::make('evaluation_score')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('approval_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),

                TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('approval_status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Organization $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approve();
                        Notification::make()
                            ->title('Organization approved')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Organization $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->reject();
                        Notification::make()
                            ->title('Organization rejected')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit'   => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}