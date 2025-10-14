<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

/**
 * Class Proveedor
 * 
 * @property int $id
 * @property string|null $nombre
 * @property string|null $tipo_servicio
 * @property string|null $contacto
 * 
 * @property Collection|Contrato[] $contratos
 *
 * @package App\Models
 */
class Proveedor extends Model
{
	use HasPanelShield;

	protected $table = 'proveedor';
	public $timestamps = false;

	protected $fillable = [
		'nombre',
		'tipo_servicio',
		'contacto'
	];

	public function contratos()
	{
		return $this->belongsToMany(Contrato::class, 'contrato_proveedor', 'proveedor_id', 'contrato_id');
	}

	public function getNombreAttribute($value)
	{
		return $value ?: 'Proveedor Sin Nombre';
	}
}
