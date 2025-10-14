<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Diagnostico
 * 
 * @property int $id
 * @property string|null $descripcion
 * @property string|null $resultado
 * @property bool|null $es_recurrente
 * @property int|null $ticket_id
 * @property int|null $usuario_id
 * 
 * @property Ticket|null $ticket
 * @property Usuario|null $usuario
 *
 * @package App\Models
 */
class Diagnostico extends Model
{
	protected $table = 'diagnostico';
	public $timestamps = false;

	protected $casts = [
		'es_recurrente' => 'bool',
		'ticket_id' => 'int',
		'usuario_id' => 'int'
	];

	protected $fillable = [
		'descripcion',
		'resultado',
		'es_recurrente',
		'ticket_id',
		'usuario_id'
	];

	public function ticket()
	{
		return $this->belongsTo(Ticket::class);
	}

	public function usuario()
	{
		return $this->belongsTo(\App\Models\User::class, 'usuario_id');
	}
}
