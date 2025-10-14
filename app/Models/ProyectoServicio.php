<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProyectoServicio
 * 
 * @property int $proyecto_id
 * @property int $servicio_id
 * 
 * @property Proyecto $proyecto
 * @property Servicio $servicio
 *
 * @package App\Models
 */
class ProyectoServicio extends Model
{
	protected $table = 'proyecto_servicio';
	public $timestamps = false;

	protected $fillable = [
		'proyecto_id',
		'servicio_id'
	];

	protected $casts = [
		'proyecto_id' => 'int',
		'servicio_id' => 'int'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}

	public function servicio()
	{
		return $this->belongsTo(Servicio::class);
	}
}
