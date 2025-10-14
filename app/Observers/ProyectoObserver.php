<?php

namespace App\Observers;

use App\Models\Proyecto;

class ProyectoObserver
{
    public function created(Proyecto $proyecto)
    {
        $proyecto->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Proyecto creado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Proyecto $proyecto)
    {
        $changes = [];
        $original = $proyecto->getOriginal();

        // Detectar cambio de estado
        if ($proyecto->estado !== $original['estado']) {
            $changes[] = "Estado cambiado de '{$original['estado']}' a '{$proyecto->estado}'";
            if (in_array($proyecto->estado, ['Cerrado', 'Cancelado'])) {
                $changes[] = "Proyecto marcado como {$proyecto->estado}";
            }
        }

        // Detectar otros cambios de campos importantes
        $campos = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'objetivos', 'riesgos'];
        foreach ($campos as $campo) {
            if ($proyecto->$campo !== $original[$campo]) {
                $changes[] = ucfirst($campo) . " cambiado de '{$original[$campo]}' a '{$proyecto->$campo}'";
            }
        }

        // Si hubo cambios, registrar en historial
        if (!empty($changes)) {
            $proyecto->historiales()->create([
                'fecha' => now(),
                'descripcion' => implode('; ', $changes),
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    public function deleted(Proyecto $proyecto)
    {
        $proyecto->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Proyecto eliminado',
            'usuario_id' => auth()->id(),
        ]);
    }
}