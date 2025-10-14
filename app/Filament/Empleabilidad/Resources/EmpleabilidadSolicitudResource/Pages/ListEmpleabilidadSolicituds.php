<?php

namespace App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource\Pages;

use App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource;
use Filament\Resources\Pages\ListRecords;

class ListEmpleabilidadSolicituds extends ListRecords
{
    protected static string $resource = EmpleabilidadSolicitudResource::class;

    public function mount(): void
    {
        // Redirigir directamente al formulario de creación
        $this->redirect($this->getResource()::getUrl('create'));
    }
}