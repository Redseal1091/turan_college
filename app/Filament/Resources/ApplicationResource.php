<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Приемная комиссия';

    protected static ?string $navigationLabel = 'Заявки абитуриентов (CRM)';

    protected static ?string $modelLabel = 'Заявка';

    protected static ?string $pluralModelLabel = 'Заявки абитуриентов';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Новые необработанные заявки';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Данные абитуриента')
                    ->schema([
                        TextInput::make('applicant_name')
                            ->label('ФИО абитуриента')
                            ->required(),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email(),
                        TextInput::make('iin')
                            ->label('ИИН')
                            ->maxLength(12),
                        Select::make('specialty_id')
                            ->label('Выбранная специальность')
                            ->relationship('specialty', 'title')
                            ->searchable()
                            ->preload(),
                        Select::make('base_education')
                            ->label('База поступления')
                            ->options([
                                '9_grade' => 'После 9 класса',
                                '11_grade' => 'После 11 класса',
                            ])
                            ->default('9_grade'),
                        Select::make('status')
                            ->label('Статус CRM')
                            ->options([
                                'new' => 'Новая заявка',
                                'in_review' => 'В обработке (звонок сделан)',
                                'accepted' => 'Зачислен / Документы приняты',
                                'rejected' => 'Отказ / не дозвонились',
                            ])
                            ->default('new')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Заметки приемной комиссии')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('applicant_name')
                    ->label('ФИО')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->url(fn ($record) => "tel:{$record->phone}"),

                TextColumn::make('specialty.title')
                    ->label('Специальность')
                    ->limit(28)
                    ->tooltip(fn ($record) => $record->specialty?->title),

                TextColumn::make('base_education')
                    ->label('База')
                    ->formatStateUsing(fn ($state) => $state === '9_grade' ? '9 класс' : '11 класс')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Статус CRM')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'in_review' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Новая',
                        'in_review' => 'В обработке',
                        'accepted' => 'Зачислен',
                        'rejected' => 'Отказ',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'new' => 'Новые',
                        'in_review' => 'В обработке',
                        'accepted' => 'Зачислены',
                        'rejected' => 'Отказы',
                    ]),
                SelectFilter::make('specialty_id')
                    ->label('Специальность')
                    ->relationship('specialty', 'title'),
                SelectFilter::make('base_education')
                    ->label('База')
                    ->options([
                        '9_grade' => 'После 9 класса',
                        '11_grade' => 'После 11 класса',
                    ]),
            ])
            ->recordActions([
                Action::make('take_in_review')
                    ->label('В работу')
                    ->icon('heroicon-m-phone-arrow-up-right')
                    ->color('info')
                    ->visible(fn (Application $record) => $record->status === 'new')
                    ->action(function (Application $record) {
                        $record->update(['status' => 'in_review']);
                        Notification::make()->title('Переведено в работу')->success()->send();
                    }),

                Action::make('accept_student')
                    ->label('Зачислить')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Application $record) => $record->status !== 'accepted')
                    ->action(function (Application $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()->title('Абитуриент зачислен')->success()->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
