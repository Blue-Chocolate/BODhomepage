<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageHeroSectionResource\Pages;
use App\Models\HomepageHeroSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageHeroSectionResource extends Resource
{
    protected static ?string $model = HomepageHeroSection::class;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Homepage';
    protected static ?string $navigationLabel = 'Hero Section';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Content')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('text')
                        ->label('Text')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('subtext')
                        ->label('Subtext')
                        ->maxLength(255),
                ]),

            Forms\Components\Section::make('Media')
                ->schema([
                    Forms\Components\FileUpload::make('background_image')
                        ->label('Background Image(s)')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('homepage/hero')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('background_video_url')
                        ->label('Background Video URL')
                        ->video()
                        ->placeholder('اضف مقطع هنا ')
                        ->disk('public')
                        ->directory('homepage/hero')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('text')
                    ->label('Text')
                    ->limit(40),

                Tables\Columns\IconColumn::make('background_video_url')
                    ->label('Has Video')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->background_video_url)),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHomepageHeroSections::route('/'),
            'create' => Pages\CreateHomepageHeroSection::route('/create'),
            'edit'   => Pages\EditHomepageHeroSection::route('/{record}/edit'),
        ];
    }
}