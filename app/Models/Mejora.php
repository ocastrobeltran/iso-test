<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Mejora extends Model
{
    protected $table = 'mejora';
    public $timestamps = false;

    protected $casts = [
        'fecha_propuesta' => 'datetime',
        'fecha_implementacion_estimada' => 'date',
        'fecha_implementacion_real' => 'date',
        'proyecto_id' => 'int',
        'usuario_id' => 'int',
        // 'origen' => 'int'
    ];

    protected $fillable = [
        'origen',
        'descripcion',
        'fecha_propuesta',
        'estado',
        'proyecto_id',
        // Nuevos campos agregados
        'beneficios_esperados',
        'recursos_necesarios',
        'prioridad',
        'fecha_implementacion_estimada',
        'fecha_implementacion_real',
        'observaciones',
        'usuario_id',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['En evaluación', 'Aprobada']);
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