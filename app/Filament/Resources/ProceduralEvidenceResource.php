<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProceduralEvidenceResource\Pages;
use App\Filament\Resources\ProceduralEvidenceResource\RelationManagers;
use App\Models\ProceduralEvidence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
class ProceduralEvidenceResource extends Resource
{
    protected static ?string $model = ProceduralEvidence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('post_id')->required()->unique(ignoreRecord: true),

        TextInput::make('title')->required()->columnSpanFull(),

        TextInput::make('slug')->required()->unique(ignoreRecord: true),

        Select::make('status')
            ->options([
                'publish' => 'Publish',
                'draft' => 'Draft',
            ]),

        Textarea::make('excerpt')->columnSpanFull(),

        RichEditor::make('content_text')->columnSpanFull(),

        TextInput::make('link')->url(),

        TextInput::make('image_url')->url(),

        TextInput::make('image_drive_file_id'),

        TextInput::make('image_drive_link')->url(),

        TextInput::make('image_file_name'),

        TextInput::make('image_upload_status'),

        TextInput::make('categories'),

        DateTimePicker::make('date'),

        DateTimePicker::make('modified'),
    ]);
}

   public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('post_id')->sortable()->searchable(),

        TextColumn::make('title')
            ->limit(50)
            ->searchable(),

        BadgeColumn::make('status'),

        TextColumn::make('categories'),

        TextColumn::make('date')->dateTime(),

        ImageColumn::make('image_url')
            ->label('Image'),

    ])->filters([
        SelectFilter::make('status')
            ->options([
                'publish' => 'Publish',
                'draft' => 'Draft',
            ])
    ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProceduralEvidence::route('/'),
            'create' => Pages\CreateProceduralEvidence::route('/create'),
            'edit' => Pages\EditProceduralEvidence::route('/{record}/edit'),
        ];
    }
}
