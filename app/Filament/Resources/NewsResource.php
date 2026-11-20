<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Медиа и события';

    protected static ?string $navigationLabel = 'Новости и статьи';

    protected static ?string $modelLabel = 'Новость';

    protected static ?string $pluralModelLabel = 'Новости';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Публикация')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок статьи')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => filled($state) ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->label('Слаг (URL)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('category')
                            ->label('Категория')
                            ->options([
                                'Новости' => 'Новости',
                                'Мероприятия' => 'Мероприятия',
                                'Достижения' => 'Достижения',
                                'Партнерство' => 'Партнерство',
                                'Студенческая жизнь' => 'Студенческая жизнь',
                            ])
                            ->default('Новости')
                            ->required(),
                        DatePicker::make('published_at')
                            ->label('Дата публикации')
                            ->default(now()),
                        TextInput::make('image_url')
                            ->label('Ссылка на изображение (обложка)')
                            ->url()
                            ->placeholder('https://images.unsplash.com/...'),
                        Toggle::make('is_featured')
                            ->label('Закрепить в топе (Главная новость)')
                            ->default(false),
                    ])->columns(2),

                Section::make('Текст статьи')
                    ->schema([
                        Textarea::make('excerpt')
                            ->label('Краткий анонс')
                            ->rows(2)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Полный текст новости')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Заголовок')->searchable()->limit(50),
                TextColumn::make('category')->label('Категория')->badge(),
                TextColumn::make('published_at')->label('Дата')->date('d.m.Y')->sortable(),
                IconColumn::make('is_featured')->label('В топе')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
