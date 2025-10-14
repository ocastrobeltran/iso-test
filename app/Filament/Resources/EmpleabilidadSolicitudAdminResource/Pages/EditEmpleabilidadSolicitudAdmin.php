<?php

namespace App\Filament\Resources\EmpleabilidadSolicitudAdminResource\Pages;

use App\Filament\Resources\EmpleabilidadSolicitudAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpleabilidadSolicitudAdmin extends EditRecord
{
    protected static string $resource = EmpleabilidadSolicitudAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
