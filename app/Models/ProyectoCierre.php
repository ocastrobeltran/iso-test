<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyectoCierre extends Model
{
    protected $table = 'proyecto_cierres';

    protected $fillable = [
        'proyecto_id',
        'periodo',
        'responsable_nombre',
        'responsable_cargo',
        'resumen_general',
        'kpi_desviacion_tiempo_pct',
        'kpi_cumplimiento_cronograma_pct',
        'kpi_cumplimiento_sla_pct',
        'kpi_roi_pct',
        'kpi_nps',
        'kpi_documentacion_completa_pct',
        'riesgos_identificados',
        'oportunidades_mejora',
        'lecciones_aprendidas',
        'acciones_correctivas',
        'recomendaciones_cierre',
        'aprobado',
        'aprobado_por',
        'aprobado_at',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
}