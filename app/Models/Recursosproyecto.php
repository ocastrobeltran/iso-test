<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Recursosproyecto
 * 
 * @property int $id
 * @property string|null $nombre
 * @property string|null $tipo
 * @property string|null $ubicacion
 * @property int|null $proyecto_id
 * 
 * @property Proyecto|null $proyecto
 *
 * @package App\Models
 */
class Recursosproyecto extends Model
{
	protected $table = 'recursosproyectos';
	public $timestamps = false;

	protected $casts = [
		'proyecto_id' => 'int'
	];

	protected $fillable = [
		'nombre',
		'tipo',
		'ubicacion',
		'proyecto_id'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}

	public function consumos()
	{
		return $this->belongsToMany(
			\App\Models\Detalleconsumo::class,
			'detalleconsumo_recursosproyecto',
			'recursosproyecto_id',
			'detalleconsumo_id'
		);
	}

	public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }
}
