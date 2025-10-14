<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketUsuario
 * 
 * @property int $ticket_id
 * @property int $usuario_id
 * @property string|null $rol
 * 
 * @property Usuario $usuario
 * @property Ticket $ticket
 *
 * @package App\Models
 */
class TicketUsuario extends Model
{
	protected $table = 'ticket_usuario';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ticket_id' => 'int',
		'usuario_id' => 'int'
	];

	protected $fillable = [
		'rol'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class);
	}

	public function ticket()
	{
		return $this->belongsTo(Ticket::class);
	}
}
