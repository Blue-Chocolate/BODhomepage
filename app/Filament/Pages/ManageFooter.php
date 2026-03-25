<?php

// app/Filament/Pages/ManageFooter.php
namespace App\Filament\Pages;

use App\Models\FooterSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageFooter extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'إعدادات الفوتر';
    protected static ?string $navigationGroup = 'Footer';
    protected static string  $view            = 'filament.pages.manage-footer';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(FooterSetting::getInstance()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الشعار والتعريف')->schema([
                Forms\Components\FileUpload::make('logo_image')
                    ->label('شعار الفوتر')
                    ->image()
                    ->directory('footer')
                    ->helperText('يمكن أن يختلف عن شعار الهيدر')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('tagline')
                    ->label('الشعار النصي عربي')
                    ->rows(2),

                Forms\Components\Textarea::make('tagline_en')
                    ->label('الشعار النصي إنجليزي')
                    ->rows(2),
            ])->columns(2),

            Forms\Components\Section::make('بيانات التواصل')->schema([
                Forms\Components\TextInput::make('contact_email')
                    ->label('البريد الإلكتروني')
                    ->email(),

                Forms\Components\TextInput::make('contact_phone')
                    ->label('الهاتف'),

                Forms\Components\TextInput::make('contact_address')
                    ->label('العنوان عربي'),

                Forms\Components\TextInput::make('contact_address_en')
                    ->label('العنوان إنجليزي'),

                Forms\Components\TextInput::make('contact_map_url')
                    ->label('رابط الخريطة')
                    ->url()
                    ->placeholder('https://maps.google.com/...')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('النشرة البريدية')->schema([
                Forms\Components\Toggle::make('newsletter_enabled')
                    ->label('تفعيل النشرة البريدية')
                    ->live()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('newsletter_title')
                    ->label('عنوان النشرة عربي')
                    ->visible(fn(Forms\Get $get) => $get('newsletter_enabled')),

                Forms\Components\TextInput::make('newsletter_title_en')
                    ->label('عنوان النشرة إنجليزي')
                    ->visible(fn(Forms\Get $get) => $get('newsletter_enabled')),

                Forms\Components\TextInput::make('newsletter_placeholder')
                    ->label('placeholder عربي')
                    ->visible(fn(Forms\Get $get) => $get('newsletter_enabled')),

                Forms\Components\TextInput::make('newsletter_placeholder_en')
                    ->label('placeholder إنجليزي')
                    ->visible(fn(Forms\Get $get) => $get('newsletter_enabled')),

                Forms\Components\TextInput::make('newsletter_button_text')
                    ->label('نص الزر عربي')
                    ->visible(fn(Forms\Get $get) => $get('newsletter_enabled')),

                Forms\Components\TextInput::make('newsletter_button_text_en')
                    ->label('نص الزر إنجليزي')
                    ->visible(fn(Forms\Get $get) => $get('newsletter_enabled')),
            ])->columns(2),

            Forms\Components\Section::make('حقوق النشر والإعدادات')->schema([
                Forms\Components\TextInput::make('copyright_text')
                    ->label('نص حقوق الملكية عربي'),

                Forms\Components\TextInput::make('copyright_text_en')
                    ->label('نص حقوق الملكية إنجليزي'),

                Forms\Components\Toggle::make('back_to_top_enabled')
                    ->label('إظهار زر الرجوع للأعلى')
                    ->default(true),
            ])->columns(2),
        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ إعدادات الفوتر')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        FooterSetting::getInstance()->update($this->form->getState());
        cache()->forget('footer_data');
        Notification::make()->title('تم الحفظ')->success()->send();
    }
}