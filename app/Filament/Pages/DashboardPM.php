<?php

namespace App\Filament\Pages;

use App\Models\Proyecto;
use App\Models\User;
use Filament\Pages\Page;

class DashboardPM extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Dashboard PM';
    protected static ?string $navigationGroup = 'Reportes y Dashboards';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.dashboard-pm';

    // Filtros
    public string $fecha_inicio;
    public string $fecha_fin;
    public ?int $pm_id = null;

    public function mount(): void
    {
        $this->fecha_inicio = now()->startOfYear()->toDateString();
        $this->fecha_fin = now()->endOfYear()->toDateString();
    }

    // Solo en panel Admin y para rol admin
    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Ajusta esta condición si usas otro mecanismo de roles/permisos.
        return $user && ($user->rol ?? null) === 'Técnico';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    // Opciones de PM para el filtro (solo quienes tienen proyectos)
    public function getPmOptionsProperty(): array
    {
        $ids = Proyecto::query()
            ->whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin])
            ->whereNotNull('pm_responsable_id')
            ->distinct()
            ->pluck('pm_responsable_id')
            ->filter()
            ->all();

        return User::whereIn('id', $ids)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    // Métricas por PM (agregadas)
    public function getPmMetricsProperty(): array
    {
        $query = Proyecto::with('pmResponsable')
            ->whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin]);

        if ($this->pm_id) {
            $query->where('pm_responsable_id', $this->pm_id);
        }

        $proyectos = $query->get();

        // Agrupar por PM
        $porPm = $proyectos->groupBy('pm_responsable_id')->map(function ($items, $pmId) {
            $pm = $items->first()->pmResponsable;

            $horasEstimadas = (float) $items->sum(fn ($p) => (float) ($p->horas_estimadas ?? 0));
            $horasEjecutadas = (float) $items->sum(fn ($p) => (float) ($p->horas_ejecutadas ?? 0));

            // SPI promedio por proyecto (real/planeado), ignorando divisiones por cero
            $spiProm = $items->avg(function ($p) {
                $plan = (float) ($p->porcentaje_avance_planeado ?? 0);
                $real = (float) ($p->porcentaje_avance_real ?? 0);
                return $plan > 0 ? $real / $plan : null;
            });

            $nps = (float) $items->whereNotNull('nps_cliente')->avg('nps_cliente');
            $doc = (float) $items->whereNotNull('avance_documentacion')->avg('avance_documentacion');

            $fasesEnt = (int) $items->sum('fases_entregadas');
            $fasesPlan = (int) $items->sum('fases_planeadas');

            $riesgosMit = (int) $items->sum('riesgos_mitigados');
            $riesgosId = (int) $items->sum('riesgos_identificados');

            return [
                'pm_id' => (int) $pmId,
                'pm' => $pm?->name ?? 'Sin asignar',
                'proyectos' => $items->count(),
                'horas_estimadas' => $horasEstimadas,
                'horas_ejecutadas' => $horasEjecutadas,
                'porcentaje_horas' => $horasEstimadas > 0 ? round(($horasEjecutadas / $horasEstimadas) * 100, 2) : 0,
                'spi' => round($spiProm ?? 0, 2),
                'nps' => round($nps, 1),
                'doc' => round($doc, 1),
                'fases_entregadas' => $fasesEnt,
                'fases_planeadas' => $fasesPlan,
                'cumplimiento_fases' => $fasesPlan > 0 ? round(($fasesEnt / $fasesPlan) * 100, 1) : 0,
                'riesgos_mitigados' => $riesgosMit,
                'riesgos_identificados' => $riesgosId,
                'mitigacion' => $riesgosId > 0 ? round(($riesgosMit / $riesgosId) * 100, 1) : 0,
            ];
        })->values();

        // Rankings útiles
        $rankingSpi = $porPm->sortByDesc('spi')->values()->take(5)->all();
        $rankingHoras = $porPm->sortByDesc('porcentaje_horas')->values()->take(5)->all();
        $rankingDoc = $porPm->sortByDesc('doc')->values()->take(5)->all();

        return [
            'lista' => $porPm->sortBy('pm')->values()->all(),
            'ranking_spi' => $rankingSpi,
            'ranking_horas' => $rankingHoras,
            'ranking_doc' => $rankingDoc,
            'totales' => [
                'proyectos' => $proyectos->count(),
                'horas_estimadas' => (float) $proyectos->sum('horas_estimadas'),
                'horas_ejecutadas' => (float) $proyectos->sum('horas_ejecutadas'),
                'prom_spi' => round($porPm->avg('spi') ?? 0, 2),
                'prom_doc' => round($porPm->avg('doc') ?? 0, 1),
                'prom_nps' => round($porPm->avg('nps') ?? 0, 1),
            ],
        ];
    }

    protected function getViewData(): array
    {
        return [
            'pmOptions' => $this->pm_options,
            'data' => $this->pm_metrics,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'pm_id' => $this->pm_id,
        ];
    }
}