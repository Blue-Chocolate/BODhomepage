<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\Release;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReleaseResource extends Resource
{
    protected static ?string $model = Release::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title_guess';

    // -------------------------------------------------------------------------
    // FORM
    // -------------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('edition_number')
                        ->label('Edition Number')
                        ->numeric()
                        ->required(),

                    Forms\Components\TextInput::make('row_number')
                        ->label('Row Number')
                        ->numeric(),

                    Forms\Components\TextInput::make('title_guess')
                        ->label('Title')
                        ->columnSpanFull()
                        ->maxLength(500),

                    Forms\Components\TextInput::make('button_text')
                        ->label('Button Text')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('image_url')
                        ->label('Image URL')
                        ->url()
                        ->maxLength(2048),
                ]),

            Forms\Components\Section::make('File Links')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('file_url')
                        ->label('File URL (Google Drive view)')
                        ->url()
                        ->maxLength(2048),

                    Forms\Components\TextInput::make('direct_download_url')
                        ->label('Direct Download URL')
                        ->url()
                        ->maxLength(2048),
                ]),

            Forms\Components\Section::make('Card Text')
                ->schema([
                    Forms\Components\Textarea::make('card_text')
                        ->label('Card Text')
                        ->rows(8)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // TABLE
    // -------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('edition_number')
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Cover')
                    ->width(60)
                    ->height(60),

                Tables\Columns\TextColumn::make('edition_number')
                    ->label('Edition')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('title_guess')
                    ->label('Title')
                    ->searchable()
                    ->limit(60),

                Tables\Columns\TextColumn::make('button_text')
                    ->label('Button')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('file_url')
                    ->label('Has File')
                    ->boolean()
                    ->getStateUsing(fn ($record) => (bool) $record->file_url),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn (Release $record) => $record->direct_download_url)
                        ->openUrlInNewTab()
                        ->visible(fn (Release $record) => (bool) $record->direct_download_url),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // -------------------------------------------------------------------------
    // PAGES
    // -------------------------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReleases::route('/'),
            'create' => Pages\CreateRelease::route('/create'),
            'edit'   => Pages\EditRelease::route('/{record}/edit'),
        ];
    }
}