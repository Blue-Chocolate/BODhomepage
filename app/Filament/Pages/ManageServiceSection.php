<?php

// app/Filament/Pages/ManageServiceSection.php
namespace App\Filament\Pages;

use App\Models\Service;
use App\Models\ServiceSectionSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ManageServiceSection extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'قسم الخدمات';
    protected static ?string $navigationGroup = 'الخدمات';
    protected static string  $view            = 'filament.pages.manage-service-section';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ServiceSectionSetting::getInstance()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('عنوان القسم والمقدمة')
                ->description('يظهر في أعلى قسم الخدمات في الصفحة الرئيسية')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان عربي')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('title_en')
                        ->label('العنوان إنجليزي')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('subtitle')
                        ->label('النص التمهيدي عربي')
                        ->rows(3),

                    Forms\Components\Textarea::make('subtitle_en')
                        ->label('النص التمهيدي إنجليزي')
                        ->rows(3),
                ])->columns(2),
        ])->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Service::query())
            ->heading('بطاقات الخدمات')
            ->description('إدارة الخدمات المعروضة — اسحب لإعادة الترتيب')
           ->columns([
    Tables\Columns\TextColumn::make('sort_order')
        ->label('#')->sortable()->width(50),

    Tables\Columns\TextColumn::make('title')
        ->label('الخدمة')->searchable()->weight('bold'),

    Tables\Columns\TextColumn::make('title_en')
        ->label('إنجليزي')->color('gray'),

    Tables\Columns\TextColumn::make('sub_services_count')
        ->label('الخدمات الفرعية')
        ->counts('subServices')
        ->badge()
        ->color('info'),

    Tables\Columns\IconColumn::make('is_active')
        ->label('مفعّل')->boolean(),
])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة خدمة')
                    ->model(Service::class)
                    ->form($this->getServiceForm())
                    ->after(fn() => cache()->forget('services_section')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getServiceForm())
                    ->after(fn() => cache()->forget('services_section')),

                Tables\Actions\Action::make('toggle')
                    ->label(fn($record) => $record->is_active ? 'إخفاء' : 'إظهار')
                    ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn($record) => $record->is_active ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_active' => ! $record->is_active]);
                        cache()->forget('services_section');
                    }),

                Tables\Actions\DeleteAction::make()
                    ->after(fn() => cache()->forget('services_section')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn() => cache()->forget('services_section')),
                ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ إعدادات القسم')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        ServiceSectionSetting::getInstance()->update($this->form->getState());

        cache()->forget('services_section');

        Notification::make()
            ->title('تم حفظ إعدادات القسم')
            ->success()
            ->send();
    }

    // فورم الخدمة مشترك بين Create و Edit
   private function getServiceForm(): array
{
    return [
        Forms\Components\Section::make('بيانات الخدمة')->schema([
            Forms\Components\TextInput::make('title')
                ->label('اسم الخدمة عربي')
                ->required(),

            Forms\Components\TextInput::make('title_en')
                ->label('اسم الخدمة إنجليزي'),

            Forms\Components\Textarea::make('description')
                ->label('وصف مختصر عربي')
                ->rows(2),

            Forms\Components\Textarea::make('description_en')
                ->label('وصف مختصر إنجليزي')
                ->rows(2),
        ])->columns(2),

        Forms\Components\Section::make('الخدمات الفرعية')->schema([
            Forms\Components\Repeater::make('subServices')
                ->relationship('subServices')
                ->label('')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('الاسم عربي')
                        ->required(),

                    Forms\Components\TextInput::make('name_en')
                        ->label('الاسم إنجليزي'),

                    Forms\Components\Textarea::make('description')
                        ->label('الوصف عربي')
                        ->rows(2),

                    Forms\Components\Textarea::make('description_en')
                        ->label('الوصف إنجليزي')
                        ->rows(2),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('الترتيب')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_active')
                        ->label('مفعّل')
                        ->default(true)
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->orderColumn('sort_order')
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn(array $state) => $state['name'] ?? 'خدمة فرعية جديدة')
                ->addActionLabel('إضافة خدمة فرعية'),
        ]),

        Forms\Components\Section::make('الإعدادات')->schema([
            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('مفعّل')
                ->default(true),
        ])->columns(2),
    ];
}
}