<?php 

// app/Filament/Resources/NewsResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model           = News::class;
    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'الأخبار';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?string $modelLabel      = 'خبر';
    protected static ?string $pluralModelLabel = 'الأخبار';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المحتوى')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) =>
                        $set('slug', Str::slug($state))
                    ),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('excerpt')
                    ->label('المقتطف')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('content_text')
                    ->label('المحتوى')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('الصورة والإعدادات')->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('الصورة')
                    ->image()
                    ->directory('news'),

                Forms\Components\TextInput::make('image_url')
                    ->label('رابط الصورة الخارجي'),

                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'publish' => 'منشور',
                        'draft'   => 'مسودة',
                        'private' => 'خاص',
                        'trash'   => 'محذوف',
                    ])
                    ->default('draft')
                    ->required(),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('تاريخ النشر'),

                Forms\Components\TextInput::make('reading_time')
                    ->label('وقت القراءة'),

                Forms\Components\TextInput::make('author_name')
                    ->label('اسم الكاتب'),
            ])->columns(2),

            Forms\Components\Section::make('التصنيفات والوسوم')->schema([
                Forms\Components\Select::make('categories')
                    ->label('التصنيفات')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),

                Forms\Components\Select::make('tags')
                    ->label('الوسوم')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('صورة')
                    ->defaultImageUrl(fn($record) => $record->image_url),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'publish',
                        'warning' => 'draft',
                        'danger'  => 'trash',
                    ]),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('التصنيف')
                    ->badge(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('النشر')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('author_name')
                    ->label('الكاتب'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'publish' => 'منشور',
                        'draft'   => 'مسودة',
                    ]),

                Tables\Filters\SelectFilter::make('categories')
                    ->label('التصنيف')
                    ->relationship('categories', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit'   => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}