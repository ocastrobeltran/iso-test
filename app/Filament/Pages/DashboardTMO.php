<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Proyecto;
use App\Models\Ticket;
use App\Models\Fee;
use App\Models\Contrato;
use App\Models\Mejora;
use Illuminate\Support\Facades\DB;

class DashboardTMO extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard TMO (Comité)';
    protected static ?string $navigationGroup = 'Reportes y Dashboards';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.dashboard-tmo';

    // Filtros
    public $fecha_inicio = null;
    public $fecha_fin = null;

    public function mount()
    {
        $this->fecha_inicio = now()->startOfYear()->toDateString();
        $this->fecha_fin = now()->endOfYear()->toDateString();
    }

    // KPIs Globales del Portafolio
    public function getKpisPortafolioProperty()
    {
        return [
            'total_proyectos' => Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])->count(),
            'proyectos_activos' => Proyecto::whereIn('estado', ['En ejecución', 'QA'])->count(),
            'proyectos_finalizados' => Proyecto::where('estado', 'Finalizado')
                ->whereBetween('fecha_fin_real', [$this->fecha_inicio, $this->fecha_fin])
                ->count(),
            'proyectos_suspendidos' => Proyecto::where('estado', 'Suspendido')->count(),
            'proyectos_cancelados' => Proyecto::where('estado', 'Cancelado')->count(),
            
            'total_contratos' => Contrato::count(),
            'valor_total_contratos' => Contrato::sum('valor'),
            
            'total_fees' => Fee::count(),
            'fees_activos' => Fee::where('estado', 'Activo')->count(),
            'revenue_fees_mensual' => Fee::where('estado', 'Activo')->sum('valor_mensual'),
        ];
    }

    // Satisfacción del Cliente (NPS Promedio)
    public function getSatisfaccionProperty()
    {
        $nps_promedio = Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])
            ->whereNotNull('nps_cliente')
            ->avg('nps_cliente');

        $distribucion = Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])
            ->whereNotNull('nps_cliente')
            ->selectRaw('
                SUM(CASE WHEN nps_cliente >= 9 THEN 1 ELSE 0 END) as promotores,
                SUM(CASE WHEN nps_cliente BETWEEN 7 AND 8 THEN 1 ELSE 0 END) as pasivos,
                SUM(CASE WHEN nps_cliente <= 6 THEN 1 ELSE 0 END) as detractores
            ')
            ->first();

        return [
            'nps_promedio' => round($nps_promedio ?? 0, 1),
            'promotores' => $distribucion->promotores ?? 0,
            'pasivos' => $distribucion->pasivos ?? 0,
            'detractores' => $distribucion->detractores ?? 0,
        ];
    }

    // Eficiencia (SPI, Horas, Documentación)
    public function getEficienciaProperty()
    {
        $proyectos = Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])->get();

        $total_horas_estimadas = $proyectos->sum('horas_estimadas');
        $total_horas_ejecutadas = $proyectos->sum('horas_ejecutadas');
        
        $spi_promedio = $proyectos->avg(function($p) {
            if ($p->porcentaje_avance_planeado > 0) {
                return $p->porcentaje_avance_real / $p->porcentaje_avance_planeado;
            }
            return null;
        });

        $documentacion_promedio = $proyectos->whereNotNull('avance_documentacion')->avg('avance_documentacion');

        return [
            'spi_promedio' => round($spi_promedio ?? 0, 2),
            'total_horas_estimadas' => $total_horas_estimadas,
            'total_horas_ejecutadas' => $total_horas_ejecutadas,
            'eficiencia_horas' => $total_horas_estimadas > 0 
                ? round(($total_horas_ejecutadas / $total_horas_estimadas) * 100, 2) 
                : 0,
            'documentacion_promedio' => round($documentacion_promedio ?? 0, 1),
        ];
    }

    // Incidentes (Tickets)
    public function getIncidentesProperty()
    {
        $base = Ticket::query()
            ->whereBetween('fecha_creacion', [$this->fecha_inicio, $this->fecha_fin]);

        // Promedio en horas (usando segundos para mantener decimales)
        $promedioSegundos = (clone $base)
            ->whereNotNull('fecha_creacion')
            ->where(function ($q) {
                $q->whereNotNull('fecha_resolucion')
                  ->orWhereNotNull('fecha_cierre');
            })
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, fecha_creacion, COALESCE(fecha_resolucion, fecha_cierre))) as prom')
            ->value('prom') ?? 0;

        return [
            'total_tickets' => (clone $base)->count(),
            'tickets_abiertos' => (clone $base)->where('estado', 'Abierto')->count(),
            'tickets_en_progreso' => (clone $base)->where('estado', 'En Progreso')->count(),
            'tickets_resueltos' => (clone $base)->where('estado', 'Resuelto')->count(),
            'tickets_cerrados' => (clone $base)->where('estado', 'Cerrado')->count(),
            'tiempo_resolucion_promedio' => round($promedioSegundos / 3600, 2),
        ];
    }

    // Oportunidades de Mejora
    public function getOportunidadesProperty()
    {
        $mejoras = Mejora::whereBetween('fecha_propuesta', [$this->fecha_inicio, $this->fecha_fin]);

        return [
            'total_mejoras' => (clone $mejoras)->count(),
            'mejoras_implementadas' => (clone $mejoras)->where('estado', 'Implementada')->count(),
            'mejoras_pendientes' => (clone $mejoras)->whereIn('estado', ['Propuesta', 'En Análisis', 'Aprobada'])->count(),
            'mejoras_rechazadas' => (clone $mejoras)->where('estado', 'Rechazada')->count(),
        ];
    }

    // Riesgos y Fases
    public function getRiesgosFasesProperty()
    {
        $proyectos = Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])->get();

        $total_riesgos_identificados = $proyectos->sum('riesgos_identificados');
        $total_riesgos_mitigados = $proyectos->sum('riesgos_mitigados');

        $total_fases_planeadas = $proyectos->sum('fases_planeadas');
        $total_fases_entregadas = $proyectos->sum('fases_entregadas');

        return [
            'riesgos_identificados' => $total_riesgos_identificados,
            'riesgos_mitigados' => $total_riesgos_mitigados,
            'porcentaje_mitigacion' => $total_riesgos_identificados > 0 
                ? round(($total_riesgos_mitigados / $total_riesgos_identificados) * 100, 1) 
                : 0,
            'fases_planeadas' => $total_fases_planeadas,
            'fases_entregadas' => $total_fases_entregadas,
            'porcentaje_cumplimiento_fases' => $total_fases_planeadas > 0 
                ? round(($total_fases_entregadas / $total_fases_planeadas) * 100, 1) 
                : 0,
        ];
    }

    // Top 5 Proyectos por Avance
    public function getTopProyectosProperty()
    {
        return Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])
            ->whereIn('estado', ['En ejecución', 'QA'])
            ->orderBy('porcentaje_avance_real', 'desc')
            ->take(5)
            ->get();
    }

    // Proyectos con Alertas (retrasos, sobre-consumo, riesgos)
    public function getProyectosConAlertasProperty()
    {
        return Proyecto::whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])
            ->where(function($q) {
                $q->whereRaw('porcentaje_avance_real < porcentaje_avance_planeado * 0.9') // retraso
                  ->orWhereRaw('horas_ejecutadas > horas_estimadas * 1.1') // sobre-consumo
                  ->orWhereRaw('riesgos_mitigados < riesgos_identificados * 0.5'); // riesgos altos
            })
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'kpis_portafolio' => $this->kpis_portafolio,
            'satisfaccion' => $this->satisfaccion,
            'eficiencia' => $this->eficiencia,
            'incidentes' => $this->incidentes,
            'oportunidades' => $this->oportunidades,
            'riesgos_fases' => $this->riesgos_fases,
            'top_proyectos' => $this->top_proyectos,
            'proyectos_alertas' => $this->proyectos_con_alertas,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ];
    }
}