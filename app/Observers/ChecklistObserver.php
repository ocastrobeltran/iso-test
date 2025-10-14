<?php
namespace App\Observers;

use App\Models\Checklist;

class ChecklistObserver
{
    public function created(Checklist $checklist)
    {
        $checklist->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Checklist creado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Checklist $checklist)
    {
        $checklist->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Checklist actualizado',
            'usuario_id' => auth()->id(),
        ]);
    }

    public function deleted(Checklist $checklist)
    {
        $checklist->historiales()->create([
            'fecha' => now(),
            'descripcion' => 'Checklist eliminado',
            'usuario_id' => auth()->id(),
        ]);
    }
}