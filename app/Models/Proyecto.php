<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Proyecto
 * 
 * @property int $id
 * @property string|null $nombre
 * @property string|null $estado
 * @property string|null $descripcion
 * @property Carbon|null $fecha_inicio
 * @property Carbon|null $fecha_fin
 * @property string|null $objetivos
 * @property string|null $riesgos
 * @property int|null $contrato_id
 * @property int|null $pm_responsable_id
 * @property Carbon|null $fecha_inicio_planificada
 * @property Carbon|null $fecha_fin_planificada
 * @property Carbon|null $fecha_fin_real
 * @property int|null $duracion_estimada
 * @property int|null $duracion_real
 * @property int|null $horas_estimadas
 * @property int|null $horas_ejecutadas
 * @property float|null $porcentaje_avance_real
 * @property float|null $porcentaje_avance_planeado
 * @property float|null $documentacion_completa
 * @property int|null $nps_cliente
 * @property string|null $mejoras_continuas
 * @property string|null $origen
 * @property int|null $riesgos_identificados
 * @property int|null $riesgos_mitigados
 * @property int|null $fases_planeadas
 * @property int|null $fases_entregadas
 * @property string|null $notas
 * 
 * @property Collection|Cambio[] $cambios
 * @property Collection|Checklist[] $checklists
 * @property Collection|Checklist[] $checklistDocumentacion
 * @property Collection|Configuracion[] $configuracions
 * @property Collection|Cronograma[] $cronogramas
 * @property Collection|Historial[] $historials
 * @property Collection|Mejora[] $mejoras
 * @property Contrato|null $contrato
 * @property User|null $pmResponsable
 * @property Collection|Detalleconsumo[] $detalleconsumos
 * @property Collection|Servicio[] $servicios
 * @property Collection|Ticket[] $tickets
 * @property Collection|Recursosproyecto[] $recursosproyectos
 *
 * @package App\Models
 */
class Proyecto extends Model
{
    protected $table = 'proyecto';
    public $timestamps = false;

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_inicio_planificada' => 'datetime',
        'fecha_fin_planificada' => 'datetime',
        'fecha_fin_real' => 'datetime',
        'duracion_estimada' => 'integer',
        'duracion_real' => 'integer',
        'horas_estimadas' => 'integer',
        'horas_ejecutadas' => 'integer',
        'porcentaje_avance_real' => 'decimal:2',
        'porcentaje_avance_planeado' => 'decimal:2',
        'documentacion_completa' => 'decimal:2',
        'nps_cliente' => 'integer',
        'mejoras_continuas' => 'string',
        'origen' => 'string',
        'riesgos_identificados' => 'integer',
        'riesgos_mitigados' => 'integer',
        'fases_planeadas' => 'integer',
        'fases_entregadas' => 'integer',
    ];

    protected $fillable = [
        'nombre',
        'estado',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'objetivos',
        'riesgos',
        'contrato_id',
        'pm_responsable_id',
        'fecha_inicio_planificada',
        'fecha_fin_planificada',
        'fecha_fin_real',
        'duracion_estimada',
        'duracion_real',
        'horas_estimadas',
        'horas_ejecutadas',
        'porcentaje_avance_real',
        'porcentaje_avance_planeado',
        'documentacion_completa',
        'nps_cliente',
        'mejoras_continuas',
        'origen',
        'riesgos_identificados',
        'riesgos_mitigados',
        'fases_planeadas',
        'fases_entregadas',
        'notas',
    ];

	public function cambios()
	{
		return $this->hasMany(Cambio::class);
	}

	public function checklists()
	{
		return $this->hasMany(Checklist::class);
	}

	public function configuracions()
	{
		return $this->hasMany(Configuracion::class);
	}

	public function cronogramas()
	{
		return $this->hasMany(Cronograma::class);
	}

	public function historials()
	{
		return $this->hasMany(Historial::class);
	}

	public function mejoras()
	{
		return $this->hasMany(Mejora::class);
	}

	// Relación uno a uno con contrato principal
    public function contrato()
    {
        return $this->belongsTo(\App\Models\Contrato::class, 'contrato_id');
    }

    // Relación uno a uno con usuario (PM responsable)
    public function pmResponsable()
    {
        return $this->belongsTo(\App\Models\User::class, 'pm_responsable_id');
    }

	// public function contratos()
	// {
	// 	return $this->belongsToMany(\App\Models\Contrato::class, 'proyecto_contrato', 'proyecto_id', 'contrato_id');
	// }

	public function detalleconsumos()
	{
		return $this->belongsToMany(Detalleconsumo::class, 'proyecto_detalleconsumo', 'proyecto_id', 'detalle_id');
	}

	public function servicios()
	{
		return $this->belongsToMany(Servicio::class);
	}

	public function tickets()
	{
		return $this->belongsToMany(
			\App\Models\Ticket::class,
			'proyecto_ticket', // tabla pivote correcta
			'proyecto_id',     // clave foránea local
			'ticket_id'        // clave foránea relacionada
		);
	}

	public function recursosproyectos()
	{
		return $this->hasMany(Recursosproyecto::class);
	}

	public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }

	public function getAvanceDocumentacionAttribute()
    {
        $checklists = $this->checklists;
        if ($checklists->isEmpty()) {
            return null;
        }
        $promedio = $checklists->avg('porcentaje_completado');
        return round($promedio, 1);
    }

	/**
     * Porcentaje de riesgos mitigados respecto a los identificados.
     */
    public function getPorcentajeRiesgosMitigadosAttribute()
    {
        if ($this->riesgos_identificados > 0) {
            return round(($this->riesgos_mitigados / $this->riesgos_identificados) * 100, 1);
        }
        return null;
    }

    /**
     * Porcentaje de fases entregadas respecto a las planeadas.
     */
    public function getPorcentajeFasesEntregadasAttribute()
    {
        if ($this->fases_planeadas > 0) {
            return round(($this->fases_entregadas / $this->fases_planeadas) * 100, 1);
        }
        return null;
    }
}
