<?php

namespace App\Filament\Resources\CambioResource\Pages;

use App\Filament\Resources\CambioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCambios extends ListRecords
{
    protected static string $resource = CambioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // Eliminar cualquier referencia a widgets no existentes
    protected function getHeaderWidgets(): array
    {
        return [];
    }
}