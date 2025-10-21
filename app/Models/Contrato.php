<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Contrato
 * 
 * @property int $id
 * @property int|null $total_horas
 * @property string|null $estado
 * @property int|null $cliente_id
 * 
 * @property Usuario|null $usuario
 * @property Collection|Proveedor[] $proveedors
 * @property Collection|Proyecto[] $proyectos
 *
 * @package App\Models
 */
class Contrato extends Model
{
	protected $table = 'contrato';
	public $timestamps = false;

	protected $casts = [
		'total_horas' => 'int',
		'cliente_id' => 'int'
	];

	protected $fillable = [
        'titulo',
        'cotizacion',
        'valor',
        'etapa',
        'estado_factura',
        'cliente_id',
    ];

	public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function proveedors()
    {
        return $this->belongsToMany(Proveedor::class, 'contrato_proveedor', 'contrato_id', 'proveedor_id');
    }

    public function recursos()
    {
        return $this->belongsToMany(\App\Models\User::class, 'contrato_recurso', 'contrato_id', 'user_id')
            ->withPivot('horas_asignadas');
    }

    public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }

    public function proyectos()
    {
        return $this->belongsToMany(\App\Models\Proyecto::class, 'proyecto_contrato', 'contrato_id', 'proyecto_id');
    }

    public function fees()
    {
        return $this->belongsToMany(\App\Models\Fee::class, 'fee_contrato', 'contrato_id', 'fee_id');
    }
}
