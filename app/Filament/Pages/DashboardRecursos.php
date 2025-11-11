<?php

namespace App\Filament\Pages;

use App\Models\Contrato;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\ClockifyService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class DashboardRecursos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Dashboard Recursos';
    protected static ?string $navigationGroup = 'Reportes y Dashboards';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.dashboard-recursos';

    // Filtros
    public string $fecha_inicio;
    public string $fecha_fin;
    public ?int $recurso_id = null;

    // Capacidad estándar mensual (puedes ajustar)
    protected int $horasEstandarMes = 160; // ~40h/semana * 4 semanas

    public function mount(): void
    {
        $this->fecha_inicio = now()->startOfMonth()->toDateString();
        $this->fecha_fin = now()->endOfMonth()->toDateString();
    }

    // Solo en panel Admin y para rol admin
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->rol ?? null) === 'Técnico';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    // Opciones de recursos (usuarios no cliente que tienen horas asignadas)
    public function getRecursosOptionsProperty(): array
    {
        $ids = [];

        // Contratos (tabla pivote real: contrato_recurso)
        if (\Schema::hasTable('contrato_recurso')) {
            $idsContrato = \DB::table('contrato_recurso')
                ->distinct()
                ->pluck('user_id')
                ->toArray();
            $ids = array_merge($ids, $idsContrato);
        }

        // Proyectos
        $pivotProyecto = \Schema::hasTable('proyecto_recurso')
            ? 'proyecto_recurso'
            : (\Schema::hasTable('proyecto_user') ? 'proyecto_user' : null);

        if ($pivotProyecto) {
            $idsProyecto = \DB::table($pivotProyecto)
                ->distinct()
                ->pluck('user_id')
                ->toArray();
            $ids = array_merge($ids, $idsProyecto);
        }

        $ids = array_unique($ids);

        return User::whereIn('id', $ids)
            ->where('rol', '!=', 'cliente')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getRecursosMetricsProperty(): array
    {
        $clockify = app(ClockifyService::class);
        $start = Carbon::parse($this->fecha_inicio)->toIso8601String();
        $end = Carbon::parse($this->fecha_fin)->toIso8601String();

        // Horas asignadas en contratos
        $horasContratos = [];
        if (\Schema::hasTable('contrato_recurso')) {
            $horasContratos = \DB::table('contrato_recurso')
                ->selectRaw('user_id, SUM(horas_asignadas) as total')
                ->groupBy('user_id')
                ->pluck('total', 'user_id')
                ->toArray();
        }

        // Horas asignadas en proyectos (detecta pivote existente)
        $horasProyectos = [];
        $pivotProyecto = \Schema::hasTable('proyecto_recurso')
            ? 'proyecto_recurso'
            : (\Schema::hasTable('proyecto_user') ? 'proyecto_user' : null);

        if ($pivotProyecto) {
            $horasProyectos = \DB::table($pivotProyecto)
                ->selectRaw('user_id, SUM(horas_asignadas) as total')
                ->groupBy('user_id')
                ->pluck('total', 'user_id')
                ->toArray();
        }

        $allIds = array_unique(array_merge(array_keys($horasContratos), array_keys($horasProyectos)));

        if ($this->recurso_id) {
            $allIds = array_filter($allIds, fn($id) => (int)$id === (int)$this->recurso_id);
        }

        $usuarios = User::whereIn('id', $allIds)->get()->keyBy('id');
        $metrics = [];
        $totalAsignadas = 0;
        $totalReportadas = 0;

        foreach ($allIds as $userId) {
            $user = $usuarios->get($userId);
            if (!$user) continue;

            $asignadasContrato = (float) ($horasContratos[$userId] ?? 0);
            $asignadasProyecto = (float) ($horasProyectos[$userId] ?? 0);
            $totalAsignado = $asignadasContrato + $asignadasProyecto;

            $reportadas = 0;
            if (!empty($user->clockify_user_id)) {
                $entries = $clockify->getUserTimeEntries($user->clockify_user_id, $start, $end);
                foreach ($entries as $e) {
                    $dur = data_get($e, 'timeInterval.duration', 'PT0S');
                    $reportadas += $this->iso8601ToSeconds($dur) / 3600;
                }
            }
            $reportadas = round($reportadas, 2);

            $utilizacion = $this->horasEstandarMes > 0
                ? round(($reportadas / $this->horasEstandarMes) * 100, 1)
                : 0;

            $cumplimiento = $totalAsignado > 0
                ? round(($reportadas / $totalAsignado) * 100, 1)
                : 0;

            $estado = 'optimo';
            if ($utilizacion >= 110) $estado = 'sobreutilizado';
            elseif ($utilizacion <= 70) $estado = 'subutilizado';

            $totalAsignadas += $totalAsignado;
            $totalReportadas += $reportadas;

            $metrics[] = [
                'user_id' => $userId,
                'nombre' => $user->name,
                'email' => $user->email,
                'horas_asignadas_contratos' => $asignadasContrato,
                'horas_asignadas_proyectos' => $asignadasProyecto,
                'horas_asignadas_total' => $totalAsignado,
                'horas_reportadas' => $reportadas,
                'horas_disponibles' => max($totalAsignado - $reportadas, 0),
                'utilizacion' => $utilizacion,
                'cumplimiento' => $cumplimiento,
                'estado' => $estado,
                'capacidad_estandar' => $this->horasEstandarMes,
            ];
        }

        usort($metrics, function ($a, $b) {
            $order = ['sobreutilizado' => 1, 'optimo' => 2, 'subutilizado' => 3];
            $cmp = ($order[$a['estado']] ?? 99) <=> ($order[$b['estado']] ?? 99);
            return $cmp !== 0 ? $cmp : strcmp($a['nombre'], $b['nombre']);
        });

        $redistribucion = $this->calcularRedistribucion($metrics);

        return [
            'lista' => $metrics,
            'totales' => [
                'recursos' => count($metrics),
                'horas_asignadas' => round($totalAsignadas, 2),
                'horas_reportadas' => round($totalReportadas, 2),
                'utilizacion_promedio' => count($metrics)
                    ? round(collect($metrics)->avg('utilizacion'), 1)
                    : 0,
            ],
            'redistribucion' => $redistribucion,
        ];
    }

    // Lógica simple de redistribución: quién tiene carga alta y quién tiene disponibilidad
    protected function calcularRedistribucion(array $metrics): array
    {
        $sobre = collect($metrics)->where('estado', 'sobreutilizado')->sortByDesc('utilizacion')->values()->all();
        $sub = collect($metrics)->where('estado', 'subutilizado')->sortBy('utilizacion')->values()->all();

        $sugerencias = [];

        foreach ($sobre as $s) {
            foreach ($sub as $d) {
                if ($d['horas_disponibles'] > 0) {
                    $horasReasignar = min(
                        $s['horas_reportadas'] - $s['horas_asignadas_total'], // exceso
                        $d['horas_disponibles'] // disponibilidad
                    );

                    if ($horasReasignar > 0) {
                        $sugerencias[] = [
                            'desde' => $s['nombre'],
                            'hacia' => $d['nombre'],
                            'horas' => round($horasReasignar, 2),
                        ];
                    }
                }
            }
        }

        return $sugerencias;
    }

    protected function iso8601ToSeconds(string $duration): int
    {
        if (!$duration) return 0;
        try {
            $i = new \DateInterval($duration);
            $days = ($i->y * 365) + ($i->m * 30) + $i->d;
            return ($days * 86400) + ($i->h * 3600) + ($i->i * 60) + $i->s;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    protected function getViewData(): array
    {
        return [
            'recursosOptions' => $this->recursos_options,
            'data' => $this->recursos_metrics,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'recurso_id' => $this->recurso_id,
        ];
    }
}