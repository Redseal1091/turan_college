<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestApplicationsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Оперативный CRM-список: Свежие заявки абитуриентов';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Application::query()->latest('id')
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Время')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('applicant_name')
                    ->label('ФИО абитуриента')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->icon('heroicon-m-phone')
                    ->url(fn ($record) => "tel:{$record->phone}"),

                TextColumn::make('specialty.title')
                    ->label('Специальность')
                    ->limit(28)
                    ->tooltip(fn ($record) => $record->specialty?->title),

                TextColumn::make('base_education')
                    ->label('База')
                    ->formatStateUsing(fn ($state) => $state === '9_grade' ? '9 кл.' : '11 кл.')
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
                        'in_review' => 'В работе',
                        'accepted' => 'Зачислен',
                        'rejected' => 'Отказ',
                        default => $state,
                    }),
            ])
            ->recordActions([
                Action::make('take_in_review')
                    ->label('В работу')
                    ->icon('heroicon-m-phone-arrow-up-right')
                    ->color('info')
                    ->visible(fn (Application $record) => $record->status === 'new')
                    ->action(function (Application $record) {
                        $record->update(['status' => 'in_review']);
                        Notification::make()
                            ->title('Заявка переведена в работу')
                            ->success()
                            ->send();
                    }),

                Action::make('accept_student')
                    ->label('Зачислить')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Application $record) => $record->status !== 'accepted')
                    ->action(function (Application $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title('Абитуриент зачислен')
                            ->success()
                            ->send();
                    }),

                Action::make('reject_student')
                    ->label('Отказ')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Application $record) => ! in_array($record->status, ['accepted', 'rejected'], true))
                    ->requiresConfirmation()
                    ->action(function (Application $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Заявка отклонена')
                            ->danger()
                            ->send();
                    }),
            ])
            ->paginated([5, 10, 25]);
    }
}
