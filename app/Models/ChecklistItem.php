<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $table = 'checklist_items';
    public $timestamps = true;

    protected $casts = [
        'checklist_id' => 'int',
        'completado_por' => 'int',
        'completado' => 'boolean',
        'fecha_completado' => 'datetime',
        'orden' => 'int'
    ];

    protected $fillable = [
        'checklist_id',
        'titulo',
        'descripcion',
        'orden',
        'completado',
        'fecha_completado',
        'completado_por',
        'observaciones'
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function completadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'completado_por');
    }

    // Auto-actualizar el porcentaje del checklist padre
    protected static function booted()
    {
        static::saved(function ($item) {
            $item->checklist->update([
                'porcentaje_completado' => $item->checklist->calcularPorcentaje()
            ]);
        });

        static::deleted(function ($item) {
            $item->checklist->update([
                'porcentaje_completado' => $item->checklist->calcularPorcentaje()
            ]);
        });
    }
}