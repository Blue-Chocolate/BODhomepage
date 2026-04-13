<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialInitiativeResource\Pages;
use App\Models\SocialInitiative;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialInitiativeResource extends Resource
{
    protected static ?string $model = SocialInitiative::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $modelLabel = 'Social Initiative';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')->schema([
                Forms\Components\TextInput::make('post_id')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->options(['publish' => 'Published', 'draft' => 'Draft'])
                    ->default('publish')
                    ->required(),

                Forms\Components\DateTimePicker::make('post_date')
                    ->label('Post Date'),

                Forms\Components\TextInput::make('link')
                    ->url()
                    ->maxLength(500)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Content')->schema([
                Forms\Components\Textarea::make('excerpt')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('content_text')
                    ->rows(6)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Images')->schema([
                Forms\Components\TextInput::make('image_url')->label('Featured Image URL')->columnSpanFull(),
                Forms\Components\TextInput::make('content_image_1')->label('Content Image 1 URL')->columnSpanFull(),
                Forms\Components\TextInput::make('image_drive_link')->label('Drive Link (Featured)')->columnSpanFull(),
                Forms\Components\TextInput::make('content_image_1_drive_link')->label('Drive Link (Content Image 1)')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post_id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('title')->limit(50)->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'publish', 'warning' => 'draft']),
                Tables\Columns\TextColumn::make('category_id')->label('Category'),
                Tables\Columns\TextColumn::make('post_date')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['publish' => 'Published', 'draft' => 'Draft']),
            ])
            ->defaultSort('post_date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSocialInitiatives::route('/'),
            'create' => Pages\CreateSocialInitiative::route('/create'),
            'edit'   => Pages\EditSocialInitiative::route('/{record}/edit'),
        ];
    }
}