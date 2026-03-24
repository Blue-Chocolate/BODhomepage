<?php

// app/Filament/Pages/ManageCarouselSettings.php
namespace App\Filament\Pages;

use App\Models\CarouselSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageCarouselSettings extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'إعدادات الكاروسيل';
    protected static ?string $navigationGroup = 'الموقع';
    protected static string  $view            = 'filament.pages.manage-carousel-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $t = CarouselSetting::forSection('testimonials');
        $s = CarouselSetting::forSection('success_stories');
        $this->form->fill(['testimonials' => $t->toArray(), 'success_stories' => $s->toArray()]);
    }

    public function form(Form $form): Form
    {
        $schema = fn(string $prefix, string $label) => [
            Forms\Components\Toggle::make("{$prefix}.auto_play")->label('تشغيل تلقائي'),
            Forms\Components\TextInput::make("{$prefix}.auto_play_speed")->label('السرعة (ms)')->numeric(),
            Forms\Components\TextInput::make("{$prefix}.slides_to_show")->label('عدد الشرائح')->numeric(),
            Forms\Components\Toggle::make("{$prefix}.show_dots")->label('إظهار النقاط'),
            Forms\Components\Toggle::make("{$prefix}.show_arrows")->label('إظهار الأسهم'),
        ];

        return $form->schema([
            Forms\Components\Section::make('الشهادات')->schema($schema('testimonials', 'الشهادات'))->columns(3),
            Forms\Components\Section::make('قصص النجاح')->schema($schema('success_stories', 'قصص النجاح'))->columns(3),
        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [Action::make('save')->label('حفظ')->action('save')];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        CarouselSetting::forSection('testimonials')->update($data['testimonials']);
        CarouselSetting::forSection('success_stories')->update($data['success_stories']);
        Notification::make()->title('تم الحفظ')->success()->send();
    }
}