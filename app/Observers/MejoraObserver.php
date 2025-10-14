<?php

namespace App\Observers;

use App\Models\Mejora;

class MejoraObserver
{
    public function created(Mejora $mejora)
    {
        $mejora->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Mejora creada',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Mejora $mejora)
    {
        $mejora->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Mejora actualizada',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function deleted(Mejora $mejora)
    {
        $mejora->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Mejora eliminada',
            'usuario_id' => auth()->id(),
        ]);
    }
}