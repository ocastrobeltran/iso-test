<?php

namespace App\Observers;

use App\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket)
    {
        $ticket->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Ticket creado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Ticket $ticket)
    {
        $changes = [];
        $original = $ticket->getOriginal();

        // Cambio de estado
        if ($ticket->estado !== $original['estado']) {
            $changes[] = "Estado cambiado de '{$original['estado']}' a '{$ticket->estado}'";
            if ($ticket->estado === 'Resuelto') {
                $changes[] = "Ticket marcado como Resuelto";
            }
            if ($ticket->estado === 'Cerrado') {
                $changes[] = "Ticket cerrado";
            }
        }

        // Cambio de asignación
        // if ($ticket->empleado_asignado_id !== $original['empleado_asignado_id']) {
        //     $changes[] = "Empleado asignado cambiado";
        // }

        // Cambio en solución
        if ($ticket->solucion !== $original['solucion']) {
            $changes[] = "Solución modificada";
        }

        // Otros cambios importantes (agrega los campos que desees auditar)
        // ...

        if (!empty($changes)) {
            $ticket->historiales()->create([
                'fecha' => now(),
                'descripcion' => implode('; ', $changes),
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    public function deleted(Ticket $ticket)
    {
        $ticket->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Ticket eliminado',
            'usuario_id' => auth()->id(),
        ]);
    }
}