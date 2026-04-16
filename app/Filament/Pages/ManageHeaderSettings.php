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
        Forms\Components\Section::make('Logo')->schema([
            Forms\Components\FileUpload::make('logo_image')
                ->label('Logo Image (SVG/PNG)')
                ->image()
                ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg'])
                ->directory('logo')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('logo_text')->label('Logo Text'),
            Forms\Components\TextInput::make('logo_url')->label('Logo URL'),
        ]),

        Forms\Components\Section::make('Content')->schema([
            Forms\Components\TextInput::make('headline')->label('Headline'),
            Forms\Components\KeyValue::make('subheadline')->label('Subheadline (key-value pairs)'),
            Forms\Components\KeyValue::make('text')->label('Text (key-value pairs)'),
            Forms\Components\FileUpload::make('background_image')
                ->label('Background Image')
                ->image()
                ->directory('backgrounds'),
        ]),

        Forms\Components\Section::make('Stats')->schema([
            Forms\Components\TextInput::make('organizations_count')->numeric()->label('Organizations Count'),
            Forms\Components\TextInput::make('experience_years')->numeric()->label('Experience Years'),
            Forms\Components\TextInput::make('projects_count')->numeric()->label('Projects Count'),
        ]),
    ]);
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