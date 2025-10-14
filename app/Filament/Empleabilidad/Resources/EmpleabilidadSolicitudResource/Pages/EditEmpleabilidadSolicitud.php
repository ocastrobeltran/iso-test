<?php

namespace App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource\Pages;

use App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpleabilidadSolicitud extends EditRecord
{
    protected static string $resource = EmpleabilidadSolicitudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
