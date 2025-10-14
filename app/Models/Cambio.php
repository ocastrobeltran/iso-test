<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cambio extends Model
{
    protected $table = 'cambio';
    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_aprobacion' => 'date',
        'fecha_implementacion' => 'date',
        'usuario_id' => 'int',
        'proyecto_id' => 'int'
    ];

    protected $fillable = [
        'descripcion',
        'fecha',
        'estado',
        'usuario_id',
        'proyecto_id',
        // Nuevos campos agregados
        'justificacion',
        'fecha_aprobacion',
        'fecha_implementacion',
        'prioridad',
        'impacto'
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['Pendiente', 'Aprobado']);
    }

    public function scopeDelProyecto($query, $proyectoId)
    {
        return $query->where('proyecto_id', $proyectoId);
    }
}