<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use App\Models\Detalleconsumo;
use App\Models\Contrato;
use App\Models\Proveedor;
use App\Models\Usuario;
use Filament\Forms\Components\DatePicker;

class ReporteStats extends BaseWidget
{
    public ?string $fecha_inicio = null;
    public ?string $fecha_fin = null;

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('fecha_inicio')->label('Desde'),
            DatePicker::make('fecha_fin')->label('Hasta'),
        ];
    }
    protected function getStats(): array
    {
        $tickets = \App\Models\Ticket::query();
        if ($this->fecha_inicio) $tickets->where('created_at', '>=', $this->fecha_inicio);
        if ($this->fecha_fin) $tickets->where('created_at', '<=', $this->fecha_fin);

        $total = $tickets->count();
        $abiertos = (clone $tickets)->where('estado', 'Abierto')->count();
        $cerrados = (clone $tickets)->where('estado', 'Cerrado')->count();
        $enprogreso = (clone $tickets)->where('estado', 'En Progreso')->count();
        $resuelto = (clone $tickets)->where('estado', 'Resuelto')->count();
        $total = Ticket::count();
        $cumplidos = Ticket::whereNotNull('fecha_resolucion')
            ->whereColumn('fecha_resolucion', '<=', 'fecha_cierre')
            ->count();

        return [
            Stat::make('Tickets abiertos', $abiertos),
            Stat::make('Tickets cerrados', $cerrados),
            Stat::make('Tickets en progreso', $enprogreso),
            Stat::make('Tickets resueltos', $resuelto),
            // Stat::make('Tickets abiertos', Ticket::where('estado', 'Abierto')->count()),
            // Stat::make('Tickets cerrados', Ticket::where('estado', 'Cerrado')->count()),
            Stat::make('Horas consumidas', Detalleconsumo::sum('horas')),
            Stat::make('Contratos activos', Contrato::where('estado', 'Activo')->count()),
            Stat::make('Proveedores', Proveedor::count()),
            Stat::make('Clientes', Usuario::where('rol', 'Cliente')->count()),
            Stat::make('Empleados', Usuario::where('rol', '!=', 'Cliente')->count()),
            Stat::make('SLA Cumplido (%)', $total > 0 ? round(($cumplidos / $total) * 100, 2) . '%' : 'N/A'),
        ];
    }
}