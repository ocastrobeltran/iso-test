<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProyectoTicket
 * 
 * @property int $proyecto_id
 * @property int $ticket_id
 * 
 * @property Proyecto $proyecto
 * @property Ticket $ticket
 *
 * @package App\Models
 */
class ProyectoTicket extends Model
{
	protected $table = 'proyecto_ticket';
	public $incrementing = true;
	public $primaryKey = 'id';
	public $timestamps = false;

	protected $fillable = [
		'proyecto_id',
		'ticket_id',
	];

	protected $casts = [
		'proyecto_id' => 'int',
		'ticket_id' => 'int'
	];

	public function proyecto()
	{
		return $this->belongsTo(Proyecto::class);
	}

	public function ticket()
	{
		return $this->belongsTo(Ticket::class);
	}
}
