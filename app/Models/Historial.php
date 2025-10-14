<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    protected $table = 'historial';
    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime',
        'usuario_id' => 'int',
        'historialable_id' => 'int',
    ];

    protected $fillable = [
        'fecha',
        'descripcion',
        'usuario_id',
        'historialable_id',
        'historialable_type',
    ];

    public function usuario()
	{
		return $this->belongsTo(\App\Models\User::class, 'usuario_id');
	}

    public function historialable()
    {
        return $this->morphTo();
    }
}