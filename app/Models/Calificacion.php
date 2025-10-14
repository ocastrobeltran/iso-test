<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificacion';
    public $timestamps = true;

    protected $casts = [
        'puntaje' => 'int',
        'usuario_id' => 'int',
        'ticket_id' => 'int'
    ];

    protected $fillable = [
        'puntaje',
        'comentario',
        'usuario_id',
        'ticket_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Scope para obtener calificaciones de un cliente específico
    public function scopeForCliente($query, $clienteId)
    {
        return $query->where('usuario_id', $clienteId);
    }

    public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }
}