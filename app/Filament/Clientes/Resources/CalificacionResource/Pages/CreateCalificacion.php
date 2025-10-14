<?php

namespace App\Filament\Clientes\Resources\CalificacionResource\Pages;

use App\Filament\Clientes\Resources\CalificacionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Request;

class CreateCalificacion extends CreateRecord
{
    protected static string $resource = CalificacionResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Obtener el ticket_id de la URL si existe
        $ticketId = request()->query('ticket_id');
        
        if ($ticketId) {
            // Preseleccionar el ticket en el formulario
            $this->form->fill([
                'ticket_id' => $ticketId,
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-asignar el usuario autenticado
        $data['usuario_id'] = auth()->id();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return '¡Gracias por tu calificación!';
    }
}