<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cronograma
 * 
 * @property int $id
 * @property string|null $descripcion
 * @property Carbon|null $fecha_inicio
 * @property Carbon|null $fecha_fin
 * @property string|null $estado
 * @property int|null $proyecto_id
 * @property int|null $usuario_id
 * 
 * @property Proyecto|null $proyecto
 * @property Usuario|null $usuario
 *
 * @package App\Models
 */
class Cronograma extends Model
{
	protected $table = 'cronograma';
	public $timestamps = false;

	protected $casts = [
		'fecha_inicio' => 'datetime',
		'fecha_fin' => 'datetime',
		'proyecto_id' => 'int',
		'usuario_id' => 'int'
	];

	protected $fillable = [
		'descripcion',
		'fecha_inicio',
		'fecha_fin',
		'estado',
		'proyecto_id',
		'usuario_id'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}

	public function usuario()
	{
		return $this->belongsTo(Usuario::class);
	}

	public function tareas()
	{
		return $this->hasMany(TareaCronograma::class, 'cronograma_id');
	}
}
