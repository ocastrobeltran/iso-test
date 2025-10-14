<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProyectoContrato
 * 
 * @property int $proyecto_id
 * @property int $contrato_id
 * 
 * @property Proyecto $proyecto
 * @property Contrato $contrato
 *
 * @package App\Models
 */
class ProyectoContrato extends Model
{
	protected $table = 'proyecto_contrato';
	public $timestamps = false;

	protected $fillable = [
		'proyecto_id',
		'contrato_id'
	];

	protected $casts = [
		'proyecto_id' => 'int',
		'contrato_id' => 'int'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}

	public function contrato()
	{
		return $this->belongsTo(Contrato::class);
	}
}
