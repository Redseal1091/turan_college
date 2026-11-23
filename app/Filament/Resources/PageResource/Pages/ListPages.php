<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
