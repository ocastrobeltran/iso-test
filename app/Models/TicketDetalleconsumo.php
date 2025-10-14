<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketDetalleconsumo
 * 
 * @property int $ticket_id
 * @property int $detalle_id
 * 
 * @property Ticket $ticket
 * @property Detalleconsumo $detalleconsumo
 *
 * @package App\Models
 */
class TicketDetalleconsumo extends Model
{
	protected $table = 'ticket_detalleconsumo';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ticket_id' => 'int',
		'detalle_id' => 'int'
	];

	public function ticket()
	{
		return $this->belongsTo(Ticket::class);
	}

	public function detalleconsumo()
	{
		return $this->belongsTo(Detalleconsumo::class, 'detalle_id');
	}
}
