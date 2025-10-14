<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Usuario
 * 
 * @property int $id
 * @property string|null $nombre
 * @property string|null $rol
 * 
 * @property Collection|Calificacion[] $calificacions
 * @property Collection|Cambio[] $cambios
 * @property Collection|Contrato[] $contratos
 * @property Collection|Cronograma[] $cronogramas
 * @property Collection|Detalleconsumo[] $detalleconsumos
 * @property Collection|Diagnostico[] $diagnosticos
 * @property Collection|Historial[] $historials
 * @property Collection|Ticket[] $tickets
 *
 * @package App\Models
 */
class Usuario extends Model
{
	protected $table = 'usuario';
	public $timestamps = false;

	protected $fillable = [
		'nombre',
		'rol'
	];

	public function calificacions()
	{
		return $this->hasMany(Calificacion::class);
	}

	public function cambios()
	{
		return $this->hasMany(Cambio::class);
	}

	public function contratos()
	{
		return $this->hasMany(Contrato::class, 'cliente_id');
	}

	public function cronogramas()
	{
		return $this->hasMany(Cronograma::class);
	}

	public function detalleconsumos()
	{
		return $this->hasMany(Detalleconsumo::class);
	}

	public function diagnosticos()
	{
		return $this->hasMany(Diagnostico::class);
	}

	public function historials()
	{
		return $this->hasMany(Historial::class);
	}

	public function tickets()
	{
		return $this->belongsToMany(\App\Models\Ticket::class, 'ticket_usuario', 'usuario_id', 'ticket_id')->withPivot('rol');
	}
}
