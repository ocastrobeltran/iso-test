<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Servicio
 * 
 * @property int $id
 * @property string|null $nombre
 * @property string|null $descripcion
 * @property string|null $prioridad
 * @property string|null $impacto
 * 
 * @property Collection|Proyecto[] $proyectos
 * @property Collection|Ticket[] $tickets
 *
 * @package App\Models
 */
class Servicio extends Model
{
	protected $table = 'servicio';
	public $timestamps = false;

	protected $fillable = [
		'nombre',
		'descripcion',
		'prioridad',
		'impacto'
	];

	public function proyectos()
	{
		return $this->belongsToMany(Proyecto::class);
	}

	public function tickets()
	{
		return $this->belongsToMany(Ticket::class, 'ticket_servicio');
	}
}
