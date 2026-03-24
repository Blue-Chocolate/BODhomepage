<?php

namespace App\Filament\Pages;

use App\Models\HeaderSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageHeaderSettings extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'إعدادات الهيدر';
    protected static ?string $navigationGroup = 'الموقع';
    protected static string  $view            = 'filament.pages.manage-header-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(HeaderSetting::getInstance()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('اللوجو')->schema([
                Forms\Components\FileUpload::make('logo_image')
                    ->label('صورة اللوجو (SVG / PNG)')
                    ->image()
                    ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg'])
                    ->directory('logo')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('logo_text')
                    ->label('اسم الموقع عربي'),

                Forms\Components\TextInput::make('logo_text_en')
                    ->label('اسم الموقع إنجليزي'),

                Forms\Components\TextInput::make('logo_url')
                    ->label('رابط اللوجو')
                    ->default('/'),
            ])->columns(2),

            Forms\Components\Section::make('زر CTA')->schema([
                Forms\Components\Toggle::make('cta_visible')
                    ->label('إظهار الزر')
                    ->live()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('cta_text')
                    ->label('نص الزر عربي')
                    ->visible(fn(Forms\Get $get) => $get('cta_visible')),

                Forms\Components\TextInput::make('cta_text_en')
                    ->label('نص الزر إنجليزي')
                    ->visible(fn(Forms\Get $get) => $get('cta_visible')),

                Forms\Components\TextInput::make('cta_url')
                    ->label('رابط الزر')
                    ->visible(fn(Forms\Get $get) => $get('cta_visible')),

                Forms\Components\ColorPicker::make('cta_color')
                    ->label('لون الزر')
                    ->visible(fn(Forms\Get $get) => $get('cta_visible')),

                Forms\Components\Toggle::make('cta_new_tab')
                    ->label('فتح في تبويب جديد')
                    ->visible(fn(Forms\Get $get) => $get('cta_visible')),
            ])->columns(2),

            Forms\Components\Section::make('سلوك الهيدر')->schema([
                Forms\Components\Toggle::make('is_sticky')
                    ->label('Sticky عند التمرير'),

                Forms\Components\Toggle::make('show_language_switcher')
                    ->label('إظهار محوّل اللغة'),
            ])->columns(2),

        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ الإعدادات')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        HeaderSetting::getInstance()->update($this->form->getState());

        cache()->forget('header_settings');

        Notification::make()->title('تم الحفظ')->success()->send();
    }
}