<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketServicio
 * 
 * @property int $ticket_id
 * @property int $servicio_id
 * 
 * @property Ticket $ticket
 * @property Servicio $servicio
 *
 * @package App\Models
 */
class TicketServicio extends Model
{
	protected $table = 'ticket_servicio';
	public $timestamps = false;

	protected $fillable = [
		'ticket_id',
		'servicio_id'
	];

	protected $casts = [
		'ticket_id' => 'int',
		'servicio_id' => 'int'
	];

	public function ticket()
	{
		return $this->belongsTo(Ticket::class);
	}

	public function servicio()
	{
		return $this->belongsTo(Servicio::class);
	}
}
