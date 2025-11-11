<?php

namespace App\Filament\Pages;

use App\Models\Fee;
use App\Models\Contrato;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class DashboardFees extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Dashboard Fees';
    protected static ?string $navigationGroup = 'Reportes y Dashboards';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.dashboard-fees';

    // Filtros
    public string $fecha_inicio;
    public string $fecha_fin;
    public ?string $estado = null;
    public ?int $cliente_id = null;

    // Días de anticipación para alertas de renovación
    protected int $diasAlerta = 30;

    public function mount(): void
    {
        $this->fecha_inicio = now()->startOfYear()->toDateString();
        $this->fecha_fin = now()->endOfYear()->toDateString();
    }

    // Solo en panel Admin y para rol admin
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->rol ?? null, ['admin', 'Técnico']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    // Opciones de estados
    public function getEstadosOptionsProperty(): array
    {
        return [
            'Activo' => 'Activo',
            'Inactivo' => 'Inactivo',
            'Suspendido' => 'Suspendido',
            'Cancelado' => 'Cancelado',
        ];
    }

    // Opciones de clientes
    public function getClientesOptionsProperty(): array
    {
        return Contrato::with('cliente')
            ->get()
            ->pluck('cliente.name', 'cliente_id')
            ->unique()
            ->filter()
            ->sort()
            ->toArray();
    }

    // Métricas principales
    public function getFeesMetricsProperty(): array
    {
        $query = Fee::query()
            ->with(['contrato.cliente'])
            ->whereBetween('created_at', [$this->fecha_inicio, $this->fecha_fin]);

        if ($this->estado) {
            $query->where('estado', $this->estado);
        }

        if ($this->cliente_id) {
            $query->whereHas('contrato', function($q) {
                $q->where('cliente_id', $this->cliente_id);
            });
        }

        $fees = $query->get();

        // KPIs Generales
        $totalFees = $fees->count();
        $feesActivos = $fees->where('estado', 'Activo')->count();
        $feesInactivos = $fees->where('estado', 'Inactivo')->count();
        $feesCancelados = $fees->where('estado', 'Cancelado')->count();

        // Revenue Recurrente (solo activos)
        $revenueMensual = (float) $fees->where('estado', 'Activo')->sum('valor_mensual');
        $revenueAnual = $revenueMensual * 12;

        // Consumo por periodo (horas consumidas vs disponibles)
        $horasDisponibles = (float) $fees->where('estado', 'Activo')
            ->where('is_demanda', false)
            ->sum('horas_ejecutadas'); // o horas_contratadas si existe
        $horasConsumidas = (float) $fees->where('estado', 'Activo')
            ->where('is_demanda', false)
            ->sum('horas_contratadas'); // ajusta según tu lógica
        $porcentajeConsumo = $horasDisponibles > 0 
            ? round(($horasConsumidas / $horasDisponibles) * 100, 1) 
            : 0;

        // Alertas de renovación: si tu Fee no tiene fecha_fin, usa created_at + duración del contrato
        // o simplemente omite esta sección si no aplica
        $fechaLimite = now()->addDays($this->diasAlerta);
        $feesProximosVencer = collect(); // vacío si no tienes fecha_fin
        $feesVencidos = collect();

        // Si quieres alertas basadas en contratos, ajusta aquí:
        // $feesProximosVencer = $fees->filter(function($fee) use ($fechaLimite) {
        //     $contratoFin = $fee->contrato->fecha_fin ?? null;
        //     if (!$contratoFin || $fee->estado !== 'Activo') return false;
        //     $fechaFin = Carbon::parse($contratoFin);
        //     return $fechaFin->lte($fechaLimite) && $fechaFin->gte(now());
        // })->sortBy(fn($f) => $f->contrato->fecha_fin)->values();

        // Top 5 fees por consumo (ajusta campos reales)
        $topConsumo = $fees->where('estado', 'Activo')
            ->where('is_demanda', false)
            ->filter(fn($f) => $f->horas_ejecutadas > 0)
            ->map(function($f) {
                $incluidas = (float) ($f->horas_ejecutadas ?? 0);
                $consumidas = (float) ($f->horas_contratadas ?? 0);
                return [
                    'id' => $f->id,
                    'nombre' => $f->nombre,
                    'cliente' => $f->contrato->cliente->name ?? 'Sin cliente',
                    'horas_incluidas' => $incluidas,
                    'horas_consumidas' => $consumidas,
                    'porcentaje' => $incluidas > 0 
                        ? round(($consumidas / $incluidas) * 100, 1) 
                        : 0,
                ];
            })
            ->sortByDesc('porcentaje')
            ->take(5)
            ->values()
            ->all();

        // Top 5 fees por valor mensual
        $topRevenue = $fees->where('estado', 'Activo')
            ->sortByDesc('valor_mensual')
            ->take(5)
            ->map(function($f) {
                return [
                    'id' => $f->id,
                    'nombre' => $f->nombre,
                    'cliente' => $f->contrato->cliente->name ?? 'Sin cliente',
                    'valor_mensual' => $f->valor_mensual,
                ];
            })
            ->values()
            ->all();

        // Distribución por cliente
        $porCliente = $fees->where('estado', 'Activo')
            ->groupBy(fn($f) => $f->contrato->cliente->name ?? 'Sin cliente')
            ->map(function($items, $cliente) {
                return [
                    'cliente' => $cliente,
                    'fees' => $items->count(),
                    'revenue_mensual' => (float) $items->sum('valor_mensual'),
                    'horas_incluidas' => (float) $items->where('is_demanda', false)->sum('horas_ejecutadas'),
                    'horas_consumidas' => (float) $items->where('is_demanda', false)->sum('horas_contratadas'),
                ];
            })
            ->sortByDesc('revenue_mensual')
            ->values()
            ->all();

        return [
            'totales' => [
                'total_fees' => $totalFees,
                'fees_activos' => $feesActivos,
                'fees_inactivos' => $feesInactivos,
                'fees_cancelados' => $feesCancelados,
                'revenue_mensual' => $revenueMensual,
                'revenue_anual' => $revenueAnual,
                'horas_disponibles' => $horasDisponibles,
                'horas_consumidas' => $horasConsumidas,
                'porcentaje_consumo' => $porcentajeConsumo,
            ],
            'alertas' => [
                'proximos_vencer' => $feesProximosVencer,
                'vencidos' => $feesVencidos,
                'dias_alerta' => $this->diasAlerta,
            ],
            'top_consumo' => $topConsumo,
            'top_revenue' => $topRevenue,
            'por_cliente' => $porCliente,
        ];
    }

    protected function getViewData(): array
    {
        return [
            'estadosOptions' => $this->estados_options,
            'clientesOptions' => $this->clientes_options,
            'data' => $this->fees_metrics,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
            'cliente_id' => $this->cliente_id,
        ];
    }
}