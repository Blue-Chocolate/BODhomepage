<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages;
use App\Filament\Resources\SocialLinkResource\RelationManagers;
use App\Models\SocialLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/SocialLinkResource.php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make()->schema([
            Forms\Components\Select::make('platform')
                ->label('المنصة')
                ->options([
                    'twitter'   => 'X (Twitter)',
                    'instagram' => 'Instagram',
                    'linkedin'  => 'LinkedIn',
                    'facebook'  => 'Facebook',
                    'youtube'   => 'YouTube',
                    'tiktok'    => 'TikTok',
                    'snapchat'  => 'Snapchat',
                    'whatsapp'  => 'WhatsApp',
                ])
                ->required(),

            Forms\Components\TextInput::make('label')
                ->label('الاسم المعروض')
                ->placeholder('تويتر'),

            Forms\Components\TextInput::make('url')
                ->label('الرابط')
                ->url()
                ->required(),

            Forms\Components\TextInput::make('icon')
                ->label('أيقونة')
                ->placeholder('heroicon-o-...')
                ->helperText('اختياري'),

            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('مفعّل')
                ->default(true),
        ])->columns(2),
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(60),
            Tables\Columns\TextColumn::make('platform')->label('المنصة')->badge(),
            Tables\Columns\TextColumn::make('url')->label('الرابط')->limit(40)->url(fn($r) => $r->url)->openUrlInNewTab(),
            Tables\Columns\IconColumn::make('is_active')->label('مفعّل')->boolean(),
        ])
        ->reorderable('sort_order')
        ->defaultSort('sort_order')
        ->actions([
            Tables\Actions\EditAction::make()->after(fn() => cache()->forget('footer_data')),
            Tables\Actions\DeleteAction::make()->after(fn() => cache()->forget('footer_data')),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()->after(fn() => cache()->forget('footer_data')),
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
            'index' => Pages\ListSocialLinks::route('/'),
            'create' => Pages\CreateSocialLink::route('/create'),
            'edit' => Pages\EditSocialLink::route('/{record}/edit'),
        ];
    }
}
