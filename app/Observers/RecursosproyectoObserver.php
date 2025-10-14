<?php

namespace App\Observers;

use App\Models\Recursosproyecto;

class RecursosproyectoObserver
{
    public function created(Recursosproyecto $recurso)
    {
        $recurso->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Recurso de proyecto creado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Recursosproyecto $recurso)
    {
        $recurso->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Recurso de proyecto actualizado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function deleted(Recursosproyecto $recurso)
    {
        $recurso->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Recurso de proyecto eliminado',
            'usuario_id' => auth()->id(),
        ]);
    }
}