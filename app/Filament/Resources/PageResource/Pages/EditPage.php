<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getTitle(): string|Htmlable
    {
        /** @var Page $record */
        $record = $this->getRecord();

        return "Редактор страницы: {$record->title}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_on_site')
                ->label('Открыть на сайте')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('primary')
                ->url(function (Page $record): string {
                    return $record->slug === 'home' ? '/' : '/'.$record->slug;
                })
                ->openUrlInNewTab(),
        ];
    }
}
