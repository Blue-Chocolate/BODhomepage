<?php

// app/Filament/Pages/ManageWhoAreWe.php
namespace App\Filament\Pages;

use App\Models\WhoAreWe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageWhoAreWe extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-information-circle';
    protected static ?string $navigationLabel = 'من نحن';
    protected static ?string $navigationGroup = 'الموقع';
    protected static string  $view            = 'filament.pages.manage-who-are-we';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(WhoAreWe::getInstance()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('العنوان والمحتوى الرئيسي')->schema([
                Forms\Components\TextInput::make('title')->label('العنوان عربي'),
                Forms\Components\TextInput::make('title_en')->label('العنوان إنجليزي'),
                Forms\Components\RichEditor::make('description')->label('الوصف عربي')->columnSpanFull(),
                Forms\Components\RichEditor::make('description_en')->label('الوصف إنجليزي')->columnSpanFull(),
                Forms\Components\FileUpload::make('image_path')
                    ->label('الصورة')->image()->directory('who-are-we')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('بيان الرؤية والرسالة')->schema([
                Forms\Components\Textarea::make('vision')->label('الرؤية عربي')->rows(3),
                Forms\Components\Textarea::make('vision_en')->label('الرؤية إنجليزي')->rows(3),
                Forms\Components\Textarea::make('mission')->label('الرسالة عربي')->rows(3),
                Forms\Components\Textarea::make('mission_en')->label('الرسالة إنجليزي')->rows(3),
            ])->columns(2),
        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('حفظ')->action('save'),
        ];
    }

    public function save(): void
    {
        WhoAreWe::getInstance()->update($this->form->getState());
        cache()->forget('who_are_we');
        Notification::make()->title('تم الحفظ')->success()->send();
    }
}