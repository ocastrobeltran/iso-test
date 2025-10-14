<?php

namespace App\Observers;

use App\Models\Contrato;

class ContratoObserver
{
    public function created(Contrato $contrato)
    {
        $contrato->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Contrato creado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Contrato $contrato)
    {
        $contrato->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Contrato actualizado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function deleted(Contrato $contrato)
    {
        $contrato->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Contrato eliminado',
            'usuario_id' => auth()->id(),
        ]);
    }
}