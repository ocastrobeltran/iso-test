<?php

namespace App\Filament\Resources\CronogramaResource\Pages;

use App\Filament\Resources\CronogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCronograma extends EditRecord
{
    protected static string $resource = CronogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
