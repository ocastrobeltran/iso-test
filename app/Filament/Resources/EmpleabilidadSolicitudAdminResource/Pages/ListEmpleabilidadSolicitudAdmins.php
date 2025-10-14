<?php

namespace App\Filament\Resources\EmpleabilidadSolicitudAdminResource\Pages;

use App\Filament\Resources\EmpleabilidadSolicitudAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmpleabilidadSolicitudAdmins extends ListRecords
{
    protected static string $resource = EmpleabilidadSolicitudAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
