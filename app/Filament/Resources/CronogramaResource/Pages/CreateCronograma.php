<?php

namespace App\Filament\Resources\CronogramaResource\Pages;

use App\Filament\Resources\CronogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCronograma extends CreateRecord
{
    protected static string $resource = CronogramaResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
