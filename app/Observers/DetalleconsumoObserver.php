<?php

namespace App\Observers;

use App\Models\Detalleconsumo;

class DetalleconsumoObserver
{
    public function created(Detalleconsumo $detalleconsumo)
    {
        $detalleconsumo->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Detalle de consumo creado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Detalleconsumo $detalleconsumo)
    {
        $detalleconsumo->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Detalle de consumo actualizado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function deleted(Detalleconsumo $detalleconsumo)
    {
        $detalleconsumo->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Detalle de consumo eliminado',
            'usuario_id' => auth()->id(),
        ]);
    }
}