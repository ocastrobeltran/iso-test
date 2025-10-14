<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Configuracion
 * 
 * @property int $id
 * @property string|null $version
 * @property string|null $titulo
 * @property string|null $descripcion
 * @property Carbon|null $fecha_creacion
 * @property int|null $proyecto_id
 * 
 * @property Proyecto|null $proyecto
 *
 * @package App\Models
 */
class Configuracion extends Model
{
	protected $table = 'configuracion';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'proyecto_id' => 'int'
	];

	protected $fillable = [
		'version',
		'titulo',
		'descripcion',
		'fecha_creacion',
		'proyecto_id'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}
}
