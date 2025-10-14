<?php
// app/Filament/Clientes/Resources/TicketResource/Pages/CreateTicket.php

namespace App\Filament\Clientes\Resources\TicketResource\Pages;

use App\Filament\Clientes\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['fecha_creacion'] = now();
        $data['estado'] = 'Abierto';
        return $data;
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record;
        $proyectoId = $this->data['proyecto_id'];

        // Asociar el ticket al proyecto
        $ticket->proyectos()->attach($proyectoId);

        // Asociar el ticket al cliente autenticado
        $ticket->users()->attach(auth()->id(), ['rol' => 'cliente']);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}