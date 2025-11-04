<?php

namespace App\Filament\Clientes\Resources\TicketResource\Pages;

use App\Filament\Clientes\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si el cliente cierra el ticket
        if (isset($data['estado']) && 
            $data['estado'] === 'Cerrado' && 
            !$this->record->fecha_cierre) {
            $data['fecha_cierre'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $nuevoComentario = $this->data['nuevo_comentario'] ?? null;
        if ($nuevoComentario) {
            $comentarios = $this->record->comentarios ?? [];
            $comentarios[] = [
                'usuario_id' => auth()->user()->name ?? auth()->id(),
                'contenido' => $nuevoComentario,
                'fecha' => now()->toDateTimeString(),
            ];
            $this->record->comentarios = $comentarios;
            $this->record->save();
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}