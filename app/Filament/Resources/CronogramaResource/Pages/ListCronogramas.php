<?php

namespace App\Filament\Resources\CronogramaResource\Pages;

use App\Filament\Resources\CronogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// use App\Filament\Widgets\CronogramasCalendarWidget;

class ListCronogramas extends ListRecords
{
    protected static string $resource = CronogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // CronogramasCalendarWidget::class,
        ];
    }
}
