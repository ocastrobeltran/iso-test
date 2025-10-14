<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContratoProveedor
 * 
 * @property int $contrato_id
 * @property int $proveedor_id
 * 
 * @property Proveedor $proveedor
 * @property Contrato $contrato
 *
 * @package App\Models
 */
class ContratoProveedor extends Model
{
	protected $table = 'contrato_proveedor';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'contrato_id' => 'int',
		'proveedor_id' => 'int'
	];

	public function proveedor()
	{
		return $this->belongsTo(Proveedor::class);
	}

	public function contrato()
	{
		return $this->belongsTo(Contrato::class);
	}
}
