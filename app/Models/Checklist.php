<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'checklist';
    public $timestamps = true; // Cambiar a true

    protected $casts = [
        'proyecto_id' => 'int',
        'responsable_id' => 'int',
        'fecha_vencimiento' => 'date',
        'fecha_completado' => 'datetime',
        'porcentaje_completado' => 'decimal:2'
    ];

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'prioridad',
        'criterios', // Mantener por compatibilidad
        'estado',
        'proyecto_id',
        'responsable_id',
        'fecha_vencimiento',
        'fecha_completado',
        'porcentaje_completado',
        'notas'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function responsable()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsable_id');
    }

    public function items()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('orden');
    }

    // Calcular porcentaje de completado automáticamente
    public function calcularPorcentaje()
    {
        $totalItems = $this->items()->count();
        if ($totalItems === 0) return 0;
        
        $itemsCompletados = $this->items()->where('completado', true)->count();
        return round(($itemsCompletados / $totalItems) * 100, 2);
    }

    // Verificar si está completado
    public function estaCompletado()
    {
        return $this->porcentaje_completado >= 100;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
    }

    public function scopePorVencer($query, $dias = 7)
    {
        return $query->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->where('estado', 'Activo')
                    ->where('porcentaje_completado', '<', 100);
    }

    public function scopeDelProyecto($query, $proyectoId)
    {
        return $query->where('proyecto_id', $proyectoId);
    }

    public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }
}