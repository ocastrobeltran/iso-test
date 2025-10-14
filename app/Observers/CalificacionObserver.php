<?php

namespace App\Observers;

use App\Models\Calificacion;

class CalificacionObserver
{
    public function created(Calificacion $calificacion)
    {
        $calificacion->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Calificación creada',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Calificacion $calificacion)
    {
        $calificacion->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Calificación actualizada',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function deleted(Calificacion $calificacion)
    {
        $calificacion->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Calificación eliminada',
            'usuario_id' => auth()->id(),
        ]);
    }
}