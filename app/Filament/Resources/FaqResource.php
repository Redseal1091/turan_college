<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Сайт';

    protected static ?string $navigationLabel = 'Вопросы и ответы (FAQ)';

    protected static ?string $modelLabel = 'Вопрос-ответ';

    protected static ?string $pluralModelLabel = 'FAQ';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Вопрос и ответ')
                    ->schema([
                        TextInput::make('question')
                            ->label('Вопрос')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->label('Подробный ответ')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                        Select::make('category')
                            ->label('Категория')
                            ->options([
                                'Поступление' => 'Поступление',
                                'Обучение' => 'Обучение',
                                'Общежитие' => 'Общежитие',
                                'Оплата и гранты' => 'Оплата и гранты',
                            ])
                            ->default('Поступление')
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->label('Вопрос')->searchable()->limit(60),
                TextColumn::make('category')->label('Категория')->badge(),
                TextColumn::make('sort_order')->label('Сортировка')->sortable(),
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
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
