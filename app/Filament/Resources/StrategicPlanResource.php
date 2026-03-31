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
    protected static ?string $navigationLabel = 'Strategic Plans';
    protected static ?string $modelLabel = 'Strategic Plan';
    protected static ?string $pluralModelLabel = 'Strategic Plans';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Basic Info')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        )
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('status')
                        ->options([
                            'publish' => 'Published',
                            'draft'   => 'Draft',
                            'private' => 'Private',
                        ])
                        ->default('publish')
                        ->required(),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Published At')
                        ->default(now()),

                    Forms\Components\TextInput::make('category_id')
                        ->label('Category ID')
                        ->numeric()
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Content')
                ->schema([
                    Forms\Components\Textarea::make('excerpt')
                        ->rows(3)
                        ->nullable(),

                    Forms\Components\Textarea::make('content_text')
                        ->label('Content')
                        ->rows(8)
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Images')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('image_url')
                        ->label('Featured Image URL')
                        ->url()
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_1')
                        ->label('Content Image 1 URL')
                        ->url()
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_2')
                        ->label('Content Image 2 URL')
                        ->url()
                        ->nullable(),
                ]),

            Forms\Components\Section::make('Drive Metadata')
                ->collapsed()
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('image_drive_file_id')
                        ->label('Featured Drive ID')
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_1_drive_file_id')
                        ->label('Image 1 Drive ID')
                        ->nullable(),

                    Forms\Components\TextInput::make('content_image_2_drive_file_id')
                        ->label('Image 2 Drive ID')
                        ->nullable(),

                    Forms\Components\TextInput::make('post_id')
                        ->label('Original Post ID (WP)')
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
                    ->label('Image')
                    ->circular(false)
                    ->width(60)
                    ->height(40),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'publish',
                        'warning' => 'draft',
                        'danger'  => 'private',
                    ]),

                Tables\Columns\TextColumn::make('category_id')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('post_id')
                    ->label('WP ID')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'publish' => 'Published',
                        'draft'   => 'Draft',
                        'private' => 'Private',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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