<?php

namespace App\Filament\Resources\DiagnosticoResource\Pages;

use App\Filament\Resources\DiagnosticoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiagnosticos extends ListRecords
{
    protected static string $resource = DiagnosticoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
