<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleabilidadSolicitud extends Model
{
    protected $table = 'empleabilidad_solicitudes';
    
    protected $fillable = [
        'tipo_solicitud',
        'herramienta',
        'canal',
        'quien_solicito',
        'descripcion_requerimiento',
        'solucion',
        'landing',
        'responsable_ejecucion',
        'fecha_inicio',
        'mes'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_creacion' => 'datetime',
    ];

    public $timestamps = false; // Ya tienes fecha_creacion manual
}