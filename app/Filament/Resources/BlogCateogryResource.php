<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCateogryResource\Pages;
use App\Models\BlogCateogry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BlogCateogryResource extends Resource
{
    protected static ?string $model = BlogCateogry::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Blog Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description('Fill in the category details below.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->minLength(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) =>
                                $set('slug', Str::slug($state))
                            )
                            ->placeholder('Enter category name')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(BlogCateogry::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->placeholder('auto-generated-from-name')
                            ->helperText('Must be unique. Only letters, numbers, dashes, and underscores.')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->rows(4)
                            ->placeholder('Optional: describe this category...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Name copied!')
                    ->tooltip('Click to copy'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Slug copied!'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->description)
                    ->toggleable()
                    ->placeholder('No description'),

                Tables\Columns\TextColumn::make('blogs_count')
                    ->label('Blogs')
                    ->counts('blogs')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_blogs')
                    ->label('Has Blogs')
                    ->query(fn (Builder $query) => $query->has('blogs'))
                    ->toggle(),

                Tables\Filters\Filter::make('no_blogs')
                    ->label('Empty Categories')
                    ->query(fn (Builder $query) => $query->doesntHave('blogs'))
                    ->toggle(),

                Tables\Filters\Filter::make('created_today')
                    ->label('Created Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('created_this_week')
                    ->label('Created This Week')
                    ->query(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Blog Category')
                    ->modalDescription('Are you sure you want to delete this category? This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Categories')
                        ->modalDescription('Are you sure you want to delete the selected categories? This cannot be undone.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->persistColumnSearchesInSession()
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('No Categories Yet')
            ->emptyStateDescription('Create your first blog category to get started.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create Category')
                    ->url(route('filament.admin.resources.blog-cateogries.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCateogries::route('/'),
            'create' => Pages\CreateBlogCateogry::route('/create'),
            'edit' => Pages\EditBlogCateogry::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->withCount('blogs');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Slug' => $record->slug,
            'Blogs' => $record->blogs_count ?? 0,
        ];
    }
}