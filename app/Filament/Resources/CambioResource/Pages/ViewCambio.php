<?php

namespace App\Filament\Resources\CambioResource\Pages;

use App\Filament\Resources\CambioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCambio extends ViewRecord
{
    protected static string $resource = CambioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => !in_array($record->estado, ['Aprobado', 'Rechazado'])),
        ];
    }

}