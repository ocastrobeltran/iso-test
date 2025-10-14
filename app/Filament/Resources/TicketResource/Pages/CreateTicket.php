<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['fecha_creacion'] = now();
        return $data;
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record;
        $proyectoId = $this->data['proyecto_id'];

        // Asociar el ticket al proyecto
        $ticket->proyectos()->attach($proyectoId);

        // Asociar el ticket al usuario autenticado como admin/PM
        $ticket->users()->attach(auth()->id(), ['rol' => 'admin']);

        // Asociar servicios seleccionados
        $servicios = $this->data['servicios'] ?? [];
        $ticket->servicios()->sync($servicios);
    }

    protected function afterSave()
    {
        $empleadoId = $this->form->getState()['empleado_asignado_id'] ?? null;
        if ($empleadoId) {
            $this->record->usuarios()->syncWithoutDetaching([$empleadoId => ['rol' => 'empleado']]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}