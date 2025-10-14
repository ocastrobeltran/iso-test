<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Detalleconsumo
 * 
 * @property int $id
 * @property string|null $actividad
 * @property string|null $modulo
 * @property float|null $horas
 * @property int|null $usuario_id
 * 
 * @property Usuario|null $usuario
 * @property Collection|Proyecto[] $proyectos
 * @property Collection|Ticket[] $tickets
 *
 * @package App\Models
 */
class Detalleconsumo extends Model
{
	protected $table = 'detalleconsumo';
	public $timestamps = false;

	protected $casts = [
		'horas' => 'float',
		'usuario_id' => 'int'
	];

	protected $fillable = [
		'actividad',
		'modulo',
		'horas',
		'usuario_id'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class);
	}

	public function proyectos()
	{
		return $this->belongsToMany(Proyecto::class, 'proyecto_detalleconsumo', 'detalle_id', 'proyecto_id');
	}

	public function tickets()
	{
		return $this->belongsToMany(Ticket::class, 'ticket_detalleconsumo', 'detalle_id', 'ticket_id');
	}

	public function getTipoAsociacionAttribute()
	{
		if ($this->proyectos()->exists()) {
			return 'proyecto';
		}
		if ($this->tickets()->exists()) {
			return 'ticket';
		}
		return null;
	}

	public function getAsociadoAttribute()
	{
		if ($this->tipo_asociacion === 'proyecto') {
			return $this->proyectos->pluck('nombre')->implode(', ');
		}
		if ($this->tipo_asociacion === 'ticket') {
			return $this->tickets->pluck('titulo')->implode(', ');
		}
		return '';
	}

	public function recursos()
	{
		return $this->belongsToMany(
			\App\Models\Recursosproyecto::class,
			'detalleconsumo_recursosproyecto',
			'detalleconsumo_id',
			'recursosproyecto_id'
		);
	}

	public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }
}
