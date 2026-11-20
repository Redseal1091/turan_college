<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialtyResource\Pages;
use App\Models\Specialty;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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

class SpecialtyResource extends Resource
{
    protected static ?string $model = Specialty::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Образование';

    protected static ?string $navigationLabel = 'Специальности';

    protected static ?string $modelLabel = 'Специальность';

    protected static ?string $pluralModelLabel = 'Специальности';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('code')
                            ->label('Код специальности')
                            ->required()
                            ->placeholder('06120100'),
                        TextInput::make('title')
                            ->label('Название специальности')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Слаг (URL)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('qualification')
                            ->label('Квалификация выпускника')
                            ->placeholder('Техник-программист'),
                        Select::make('department')
                            ->label('Направление / Кафедра')
                            ->options([
                                'Информационные технологии' => 'Информационные технологии',
                                'Дизайн и искусство' => 'Дизайн и искусство',
                                'Экономика и бизнес' => 'Экономика и бизнес',
                                'Сервис и туризм' => 'Сервис и туризм',
                                'Гуманитарное и правовое' => 'Гуманитарное и правовое',
                            ])
                            ->required(),
                        Select::make('base_education')
                            ->label('База поступления')
                            ->options([
                                '9_grade' => 'После 9 класса',
                                '11_grade' => 'После 11 класса',
                                'both' => 'После 9 и 11 классов',
                            ])
                            ->default('9_grade')
                            ->required(),
                        TextInput::make('duration')
                            ->label('Срок обучения')
                            ->default('2 года 10 месяцев')
                            ->required(),
                        TextInput::make('tuition_fee')
                            ->label('Стоимость за год (₸)')
                            ->numeric()
                            ->default(480000),
                        TextInput::make('grants_count')
                            ->label('Количество грантов')
                            ->numeric()
                            ->default(20),
                        Toggle::make('is_featured')
                            ->label('Показывать на главной (в топе)')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Активна для набора')
                            ->default(true),
                    ])->columns(2),

                Section::make('Описание и навыки')
                    ->schema([
                        Textarea::make('description')
                            ->label('Описание программы')
                            ->rows(4)
                            ->columnSpanFull(),
                        TagsInput::make('skills')
                            ->label('Чему научится студент (ключевые навыки)')
                            ->placeholder('Нажмите Enter для добавления навыка')
                            ->columnSpanFull(),
                        TagsInput::make('career_prospects')
                            ->label('Кем сможет работать (должности)')
                            ->placeholder('Нажмите Enter для добавления должности')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Код')->searchable(),
                TextColumn::make('title')->label('Специальность')->searchable()->sortable(),
                TextColumn::make('department')->label('Кафедра')->badge(),
                TextColumn::make('tuition_fee')->label('Стоимость (₸)')->money('KZT')->sortable(),
                TextColumn::make('grants_count')->label('Гранты')->badge(),
                IconColumn::make('is_featured')->label('На главной')->boolean(),
                IconColumn::make('is_active')->label('Набор открыт')->boolean(),
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
            ->defaultSort('id', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecialties::route('/'),
            'create' => Pages\CreateSpecialty::route('/create'),
            'edit' => Pages\EditSpecialty::route('/{record}/edit'),
        ];
    }
}
