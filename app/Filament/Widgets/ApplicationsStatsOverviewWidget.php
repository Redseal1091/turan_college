<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Specialty;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationsStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Аналитика приемной кампании 2026/2027';

    protected function getStats(): array
    {
        $total = Application::query()->count();
        $newCount = Application::query()->where('status', 'new')->count();
        $inReviewCount = Application::query()->where('status', 'in_review')->count();
        $acceptedCount = Application::query()->where('status', 'accepted')->count();

        $conversion = $total > 0 ? round(($acceptedCount / $total) * 100, 1) : 0;
        $specialtiesCount = Specialty::query()->where('is_active', true)->count();

        return [
            Stat::make('Всего заявок', (string) $total)
                ->description('Все источники (сайт, экспресс, онлайн)')
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color('primary')
                ->chart([5, 8, 12, 15, $total]),

            Stat::make('Новые лиды (требуют звонка)', (string) $newCount)
                ->description($newCount > 0 ? 'Связаться с абитуриентами' : 'Все заявки обработаны')
                ->descriptionIcon('heroicon-m-phone-arrow-up-right')
                ->color($newCount > 0 ? 'warning' : 'success')
                ->url(url('/admin/applications?tableFilters[status][value]=new')),

            Stat::make('В обработке', (string) $inReviewCount)
                ->description('Звонок совершен, сбор документов')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->chart([2, 4, 3, 6, $inReviewCount]),

            Stat::make('Приняты / Зачислены', (string) $acceptedCount)
                ->description("Конверсия: {$conversion}%")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([1, 2, 5, 8, $acceptedCount]),

            Stat::make('Активные специальности', (string) $specialtiesCount)
                ->description('Открыт прием на гранты и платное')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('gray')
                ->url(url('/admin/specialties')),
        ];
    }
}
