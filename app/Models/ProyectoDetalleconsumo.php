<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProyectoDetalleconsumo
 * 
 * @property int $proyecto_id
 * @property int $detalle_id
 * 
 * @property Proyecto $proyecto
 * @property Detalleconsumo $detalleconsumo
 *
 * @package App\Models
 */
class ProyectoDetalleconsumo extends Model
{
	protected $table = 'proyecto_detalleconsumo';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'proyecto_id' => 'int',
		'detalle_id' => 'int'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}

	public function detalleconsumo()
	{
		return $this->belongsTo(Detalleconsumo::class, 'detalle_id');
	}
}
