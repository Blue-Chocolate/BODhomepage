<?php
// app/Filament/Resources/NavItemResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\NavItemResource\Pages;
use App\Models\NavItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NavItemResource extends Resource
{
    protected static ?string $model           = NavItem::class;
    protected static ?string $navigationIcon  = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'روابط القائمة';
    protected static ?string $navigationGroup = 'الموقع';
    protected static ?string $modelLabel      = 'رابط';
    protected static ?string $pluralModelLabel = 'روابط القائمة';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('label')
                    ->label('الاسم عربي')->required(),

                Forms\Components\TextInput::make('label_en')
                    ->label('الاسم إنجليزي'),

                Forms\Components\TextInput::make('url')
                    ->label('الرابط')->required()->default('#'),

                Forms\Components\Select::make('parent_id')
                    ->label('Dropdown تابع لـ')
                    ->options(NavItem::whereNull('parent_id')->pluck('label', 'id'))
                    ->nullable()
                    ->placeholder('— رئيسي —'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('الترتيب')->numeric()->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('مفعّل')->default(true),

                Forms\Components\Toggle::make('open_in_new_tab')
                    ->label('فتح في تبويب جديد'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(60),
                Tables\Columns\TextColumn::make('label')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('url')->label('الرابط')->limit(40),
                Tables\Columns\TextColumn::make('parent.label')
                    ->label('تابع لـ')->default('رئيسي')->badge()->color('gray'),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّل')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')  // سحب وإفلات
            ->actions([
                Tables\Actions\EditAction::make()->after(fn() => cache()->forget('nav_menu')),
                Tables\Actions\DeleteAction::make()->after(fn() => cache()->forget('nav_menu')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->after(fn() => cache()->forget('nav_menu')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNavItems::route('/'),
            'create' => Pages\CreateNavItem::route('/create'),
            'edit'   => Pages\EditNavItem::route('/{record}/edit'),
        ];
    }
}