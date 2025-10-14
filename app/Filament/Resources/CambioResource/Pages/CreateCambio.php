<?php

namespace App\Filament\Resources\CambioResource\Pages;

use App\Filament\Resources\CambioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCambio extends CreateRecord
{
    protected static string $resource = CambioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cambio creado exitosamente';
    }

    // Eliminar cualquier referencia a widgets no existentes
    protected function getHeaderWidgets(): array
    {
        return [];
    }
}