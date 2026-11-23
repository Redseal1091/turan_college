<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Сайт';

    protected static ?string $navigationLabel = 'Конструктор страниц';

    protected static ?string $modelLabel = 'Страница';

    protected static ?string $pluralModelLabel = 'Конструктор страниц';

    protected static ?int $navigationSort = 0;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 12])
            ->components([
                // =============================================================
                // ЛЕВАЯ КОНТЕНТНАЯ ЗОНА (67% - 75% ширины)
                // =============================================================
                Group::make([
                    Section::make('Заголовок страницы')
                        ->schema([
                            TextInput::make('title')
                                ->label('Название страницы в админке')
                                ->required(),
                        ]),

                    // -------------------------------------------------------------
                    // 1. СЕКЦИИ ДЛЯ СТРАНИЦЫ 'home' (Главная)
                    // -------------------------------------------------------------
                    Section::make('Главный экран (Hero)')
                        ->visible(fn (?Page $record) => $record?->slug === 'home')
                        ->schema([
                            TextInput::make('content.hero.title')
                                ->label('Главный заголовок Hero')
                                ->required(),
                            TextInput::make('content.hero.countup_years')
                                ->label('Количество лет опыта (цифра)')
                                ->numeric()
                                ->default(23)
                                ->helperText('Число для анимированного счетчика'),
                            Textarea::make('content.hero.subtitle')
                                ->label('Подзаголовок / Описание')
                                ->rows(3)
                                ->helperText('Число в тексте (или {years}) будет красиво анимировано плавным счетчиком CountUp')
                                ->columnSpanFull(),
                            TextInput::make('content.hero.cta_primary')
                                ->label('Текст кнопки 1 (Основной CTA)')
                                ->default('КАК ПОСТУПИТЬ?'),
                            TextInput::make('content.hero.cta_secondary')
                                ->label('Текст кнопки 2 (Вторичный CTA)')
                                ->default('СТОИМОСТЬ ОБУЧЕНИЯ'),
                            TextInput::make('content.hero.tour_video_url')
                                ->label('Ссылка на видеотур (YouTube / Embed)')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make('Блок «Про колледж» (Превью на главной)')
                        ->visible(fn (?Page $record) => $record?->slug === 'home')
                        ->schema([
                            TextInput::make('content.about_preview.title')
                                ->label('Заголовок блока')
                                ->columnSpanFull(),
                            Textarea::make('content.about_preview.description')
                                ->label('Текст описания')
                                ->rows(2)
                                ->columnSpanFull(),
                            Repeater::make('content.about_preview.feature_cards')
                                ->label('3 карточки особенностей')
                                ->schema([
                                    TextInput::make('title')->label('Заголовок карточки')->required(),
                                    Textarea::make('desc')->label('Описание карточки')->rows(2)->required(),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Карточка')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Нижний CTA-баннер')
                        ->visible(fn (?Page $record) => $record?->slug === 'home')
                        ->schema([
                            TextInput::make('content.cta_banner.title')
                                ->label('Заголовок баннера')
                                ->columnSpanFull(),
                            TextInput::make('content.cta_banner.subtitle')
                                ->label('Подзаголовок баннера')
                                ->columnSpanFull(),
                            TextInput::make('content.cta_banner.button_text')
                                ->label('Текст на кнопке заявки')
                                ->default('Отправить заявку'),
                        ]),

                    // -------------------------------------------------------------
                    // 2. СЕКЦИИ ДЛЯ СТРАНИЦЫ 'about' (О колледже)
                    // -------------------------------------------------------------
                    Section::make('Главный экран «О колледже»')
                        ->visible(fn (?Page $record) => $record?->slug === 'about')
                        ->schema([
                            TextInput::make('content.hero.title')
                                ->label('Заголовок Hero')
                                ->columnSpanFull(),
                            Textarea::make('content.hero.subtitle')
                                ->label('Подзаголовок / Миссия')
                                ->rows(3)
                                ->columnSpanFull(),
                            TextInput::make('content.hero.license_number')
                                ->label('Номер генеральной лицензии МОН РК')
                                ->default('№ 14003946'),
                            TextInput::make('content.hero.license_date')
                                ->label('Дата выдачи лицензии')
                                ->default('от 26.03.2014 г.'),
                            TextInput::make('content.hero.countup_years')
                                ->label('Лет успешного опыта')
                                ->numeric()
                                ->default(23),
                        ])
                        ->columns(3),

                    Section::make('6 ключевых преимуществ / Столпов колледжа')
                        ->visible(fn (?Page $record) => $record?->slug === 'about')
                        ->schema([
                            Repeater::make('content.pillars')
                                ->label('Столпы и преимущества')
                                ->schema([
                                    TextInput::make('title')->label('Заголовок')->required(),
                                    Textarea::make('desc')->label('Описание')->rows(2)->required(),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Столп')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Блок введения руководства и преподавателей')
                        ->visible(fn (?Page $record) => $record?->slug === 'about')
                        ->schema([
                            TextInput::make('content.leadership_intro.title')
                                ->label('Заголовок секции')
                                ->default('Педагогический состав'),
                            Textarea::make('content.leadership_intro.desc')
                                ->label('Описание педагогического состава')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),

                    // -------------------------------------------------------------
                    // 3. СЕКЦИИ ДЛЯ СТРАНИЦЫ 'admission' (Поступление)
                    // -------------------------------------------------------------
                    Section::make('Сроки приемной кампании 2026/2027')
                        ->visible(fn (?Page $record) => $record?->slug === 'admission')
                        ->schema([
                            TextInput::make('content.deadlines.daytime_dates')
                                ->label('Сроки приема (Дневное отделение)')
                                ->default('с 25 июня по 27 августа'),
                            TextInput::make('content.deadlines.extramural_dates')
                                ->label('Сроки приема (Заочное отделение)')
                                ->default('с 25 июня по 20 сентября'),
                            TextInput::make('content.deadlines.note')
                                ->label('Учебный год / Примечание')
                                ->default('2026–2027 учебный год'),
                        ])
                        ->columns(3),

                    Section::make('4 шага поступления')
                        ->visible(fn (?Page $record) => $record?->slug === 'admission')
                        ->schema([
                            Repeater::make('content.steps')
                                ->label('Этапы поступления')
                                ->schema([
                                    TextInput::make('step_num')->label('Номер этапа')->numeric()->required(),
                                    TextInput::make('title')->label('Заголовок шага')->required(),
                                    Textarea::make('desc')->label('Описание шага')->rows(2)->required(),
                                ])
                                ->columns(3)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => ($state['step_num'] ?? '').'. '.($state['title'] ?? ''))
                                ->columnSpanFull(),
                        ]),

                    Section::make('Перечень необходимых документов')
                        ->visible(fn (?Page $record) => $record?->slug === 'admission')
                        ->schema([
                            Repeater::make('content.required_documents')
                                ->label('Список обязательных документов')
                                ->simple(
                                    TextInput::make('document')->label('Наименование документа')->required()
                                )
                                ->columnSpanFull(),
                        ]),

                    Section::make('Стоимость обучения и гранты')
                        ->visible(fn (?Page $record) => $record?->slug === 'admission')
                        ->schema([
                            Textarea::make('content.tuition_grants')
                                ->label('Текст о государственных и ректорских грантах')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),

                    Section::make('Студенческое общежитие')
                        ->visible(fn (?Page $record) => $record?->slug === 'admission')
                        ->schema([
                            TextInput::make('content.dormitory.title')
                                ->label('Заголовок блока общежития')
                                ->default('Студенческое общежитие и гранты'),
                            TextInput::make('content.dormitory.badge')
                                ->label('Бейдж')
                                ->default('Комфорт и безопасность'),
                            Textarea::make('content.dormitory.desc')
                                ->label('Описание условий проживания')
                                ->rows(3)
                                ->columnSpanFull(),
                            Repeater::make('content.dormitory.features')
                                ->label('Список удобств общежития')
                                ->simple(
                                    TextInput::make('feature')->label('Удобство / особенность')->required()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    // -------------------------------------------------------------
                    // 4. СЕКЦИИ ДЛЯ СТРАНИЦЫ 'student' (Студенту)
                    // -------------------------------------------------------------
                    Section::make('Расписание звонков (Сетка пар)')
                        ->visible(fn (?Page $record) => $record?->slug === 'student')
                        ->schema([
                            Repeater::make('content.call_schedule')
                                ->label('Сетка звонков')
                                ->schema([
                                    TextInput::make('pair')->label('Название пары / перерыва')->required(),
                                    TextInput::make('time_start')->label('Начало (ЧЧ:ММ)')->required(),
                                    TextInput::make('time_end')->label('Конец (ЧЧ:ММ)')->required(),
                                ])
                                ->columns(3)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => ($state['pair'] ?? '').' ('.($state['time_start'] ?? '').' – '.($state['time_end'] ?? '').')')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Скачивание расписаний занятий')
                        ->visible(fn (?Page $record) => $record?->slug === 'student')
                        ->schema([
                            Repeater::make('content.schedule_downloads')
                                ->label('Курсы и файлы расписаний')
                                ->schema([
                                    TextInput::make('year')->label('Курс (напр. 1 курс)')->required(),
                                    TextInput::make('info')->label('Описание')->required(),
                                    TextInput::make('file_url')->label('Ссылка на PDF / диск'),
                                ])
                                ->columns(3)
                                ->collapsible()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Студенческие клубы и сообщества')
                        ->visible(fn (?Page $record) => $record?->slug === 'student')
                        ->schema([
                            Repeater::make('content.student_clubs')
                                ->label('Студенческие клубы')
                                ->schema([
                                    TextInput::make('name')->label('Название клуба')->required(),
                                    Textarea::make('desc')->label('Описание деятельности')->rows(2)->required(),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Клуб')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Инфраструктура кампуса')
                        ->visible(fn (?Page $record) => $record?->slug === 'student')
                        ->schema([
                            TextInput::make('content.infrastructure.medical.title')->label('Медпункт: Заголовок')->default('Медицинский пункт'),
                            TextInput::make('content.infrastructure.medical.hours')->label('Медпункт: График работы'),
                            Textarea::make('content.infrastructure.medical.desc')->label('Медпункт: Описание')->rows(2)->columnSpanFull(),

                            TextInput::make('content.infrastructure.cafeteria.title')->label('Столовая: Заголовок')->default('Пункт питания (Столовая)'),
                            TextInput::make('content.infrastructure.cafeteria.hours')->label('Столовая: График работы'),
                            Textarea::make('content.infrastructure.cafeteria.desc')->label('Столовая: Описание')->rows(2)->columnSpanFull(),

                            TextInput::make('content.infrastructure.library.title')->label('Библиотека: Заголовок')->default('Библиотечный комплекс'),
                            TextInput::make('content.infrastructure.library.hours')->label('Библиотека: Режим работы'),
                            Textarea::make('content.infrastructure.library.desc')->label('Библиотека: Описание')->rows(2)->columnSpanFull(),
                        ])
                        ->columns(2),

                    // -------------------------------------------------------------
                    // 5. СЕКЦИИ ДЛЯ СТРАНИЦЫ 'career' (Карьера)
                    // -------------------------------------------------------------
                    Section::make('Ключевые метрики трудоустройства')
                        ->visible(fn (?Page $record) => $record?->slug === 'career')
                        ->schema([
                            TextInput::make('content.metrics.employment_rate')
                                ->label('Уровень занятости (напр. 95%)')
                                ->default('95%'),
                            TextInput::make('content.metrics.partners_count')
                                ->label('Число компаний-партнеров (напр. 150+)')
                                ->default('150+'),
                            Textarea::make('content.metrics.dual_education_text')
                                ->label('Текст о дуальном образовании')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make('Гид: «Как составить резюме выпускнику» (4 шага)')
                        ->visible(fn (?Page $record) => $record?->slug === 'career')
                        ->schema([
                            Repeater::make('content.resume_guide')
                                ->label('Шаги составления резюме')
                                ->schema([
                                    TextInput::make('step_num')->label('Номер шага (напр. 01)')->required(),
                                    TextInput::make('title')->label('Название шага')->required(),
                                    Textarea::make('desc')->label('Подробное описание')->rows(2)->required(),
                                ])
                                ->columns(3)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => ($state['step_num'] ?? '').'. '.($state['title'] ?? ''))
                                ->columnSpanFull(),
                        ]),

                    Section::make('Производственная практика и партнерство')
                        ->visible(fn (?Page $record) => $record?->slug === 'career')
                        ->schema([
                            Textarea::make('content.practice_info')
                                ->label('Текст о базах практики и партнерах')
                                ->rows(4)
                                ->columnSpanFull(),
                        ]),

                    // -------------------------------------------------------------
                    // 6. СЕКЦИИ ДЛЯ СТРАНИЦЫ 'contacts' (Контакты)
                    // -------------------------------------------------------------
                    Section::make('Контактные данные и адрес')
                        ->visible(fn (?Page $record) => $record?->slug === 'contacts')
                        ->schema([
                            TextInput::make('content.address')
                                ->label('Адрес колледжа')
                                ->columnSpanFull()
                                ->required(),
                            TextInput::make('content.email')
                                ->label('Электронная почта (Email)')
                                ->email()
                                ->required(),
                            TextInput::make('content.working_hours')
                                ->label('Режим работы')
                                ->required(),
                            TextInput::make('content.2gis_url')
                                ->label('Ссылка на карту 2GIS')
                                ->columnSpanFull(),
                            Repeater::make('content.phones')
                                ->label('Номера телефонов')
                                ->schema([
                                    TextInput::make('label')->label('Отдел / Назначение')->required(),
                                    TextInput::make('number')->label('Номер телефона')->tel()->required(),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => ($state['label'] ?? '').': '.($state['number'] ?? ''))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 8, '2xl' => 9]),

                // =============================================================
                // ПРАВЫЙ САЙДБАР: НАСТРОЙКИ СТРАНИЦЫ
                // =============================================================
                Group::make([
                    Section::make('Статус публикации')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Опубликована')
                                ->helperText('Включение/отключение отображения страницы на сайте')
                                ->default(true),
                        ]),

                    Section::make('Основные настройки')
                        ->schema([
                            TextInput::make('slug')
                                ->label('Слаг / URL страницы')
                                ->disabled()
                                ->dehydrated(false)
                                ->prefix('/')
                                ->helperText('Системный идентификатор маршрута'),
                            FileUpload::make('featured_image')
                                ->label('Главное изображение / Обложка')
                                ->image()
                                ->disk('public')
                                ->directory('pages')
                                ->imageEditor()
                                ->helperText('Основная обложка или фоновый баннер страницы'),
                        ]),

                    Section::make('Поисковая оптимизация (SEO)')
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->placeholder('Заголовок вкладки в браузере')
                                ->columnSpanFull(),
                            Textarea::make('seo_description')
                                ->label('SEO Description')
                                ->rows(3)
                                ->placeholder('Краткое мета-описание для поисковых систем')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Информация о ревизии')
                        ->schema([
                            Placeholder::make('created_at')
                                ->label('Создана')
                                ->content(fn (?Page $record) => $record?->created_at ? $record->created_at->format('d.m.Y H:i') : '—'),
                            Placeholder::make('updated_at')
                                ->label('Последнее изменение')
                                ->content(fn (?Page $record) => $record?->updated_at ? $record->updated_at->format('d.m.Y H:i') : '—'),
                        ])
                        ->collapsible(),
                ])
                    ->columnSpan(['default' => 12, 'lg' => 4, 'xl' => 4, '2xl' => 3])
                    ->extraAttributes(['class' => 'space-y-6 lg:sticky lg:top-20']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Обложка')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (Page $record) => $record->featured_image && str_starts_with($record->featured_image, 'images/') ? '/'.$record->featured_image : null),
                TextColumn::make('title')
                    ->label('Название страницы')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->label('Слаг / URL')
                    ->badge(),
                TextColumn::make('seo_title')
                    ->label('SEO Title')
                    ->limit(40)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ])
            ->defaultSort('id', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
