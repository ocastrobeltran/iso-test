<?php

namespace App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource\Pages;

use App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmpleabilidadSolicitud extends CreateRecord
{
    protected static string $resource = EmpleabilidadSolicitudResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Redirigir al mismo formulario después de crear
        return static::getResource()::getUrl('create');
    }
}