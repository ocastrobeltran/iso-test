<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Proyecto;
use App\Models\Ticket;
use App\Models\Diagnostico;
use App\Models\Configuracion;
use App\Models\Historial;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ReporteDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reportes y KPIs';
    protected static ?string $navigationGroup = 'Reportes y Dashboards';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.reporte-dashboard';

    // Filtros
    public $filter_proyecto_id = null;
    public $filter_estado = null;
    public $filter_fecha_inicio = null;
    public $filter_fecha_fin = null;

    // Propiedades efectivas
    public $proyecto_id = null;
    public $estado = null;
    public $fecha_inicio = null;
    public $fecha_fin = null;

    public function mount()
    {
        $this->aplicarFiltros();
    }

    public function getProyectosProperty()
    {
        return Proyecto::orderBy('nombre')->get();
    }

    public function getEstadosProperty()
    {
        return [
            'Abierto' => 'Abierto',
            'En Progreso' => 'En Progreso',
            'Resuelto' => 'Resuelto',
            'Cerrado' => 'Cerrado',
        ];
    }

    public function getTicketsQuery()
    {
        $query = Ticket::query()
            ->select('ticket.*', 'proyecto.nombre as proyecto_nombre')
            ->join('proyecto_ticket', 'ticket.id', '=', 'proyecto_ticket.ticket_id')
            ->join('proyecto', 'proyecto_ticket.proyecto_id', '=', 'proyecto.id');

        if ($this->proyecto_id) {
            $query->where('proyecto_ticket.proyecto_id', $this->proyecto_id);
        }
        if ($this->estado) {
            $query->where('ticket.estado', $this->estado);
        }
        if ($this->fecha_inicio) {
            $query->whereDate('ticket.fecha_creacion', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $query->whereDate('ticket.fecha_creacion', '<=', $this->fecha_fin);
        }

        return $query;
    }

    public function getDiagnosticosQuery()
    {
        $query = Diagnostico::query()
            ->join('ticket', 'diagnostico.ticket_id', '=', 'ticket.id')
            ->join('proyecto_ticket', 'ticket.id', '=', 'proyecto_ticket.ticket_id')
            ->join('proyecto', 'proyecto_ticket.proyecto_id', '=', 'proyecto.id');

        if ($this->proyecto_id) {
            $query->where('proyecto_ticket.proyecto_id', $this->proyecto_id);
        }
        // Elimina o comenta estas líneas:
        // if ($this->fecha_inicio) {
        //     $query->whereDate('diagnostico.fecha', '>=', $this->fecha_inicio);
        // }
        // if ($this->fecha_fin) {
        //     $query->whereDate('diagnostico.fecha', '<=', $this->fecha_fin);
        // }

        return $query;
    }

    public function getConfiguracionesQuery()
    {
        $query = Configuracion::query()
            ->join('proyecto', 'configuracion.proyecto_id', '=', 'proyecto.id');

        if ($this->proyecto_id) {
            $query->where('configuracion.proyecto_id', $this->proyecto_id);
        }
        if ($this->fecha_inicio) {
            $query->whereDate('configuracion.fecha_creacion', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $query->whereDate('configuracion.fecha_creacion', '<=', $this->fecha_fin);
        }

        return $query;
    }

    public function getKpisProperty()
    {
        $baseTickets = $this->getTicketsQuery();
        $baseDiagnosticos = $this->getDiagnosticosQuery();
        $baseConfiguraciones = $this->getConfiguracionesQuery();

        // Tickets
        $totalTickets = (clone $baseTickets)->count();
        $ticketsAbiertos = (clone $baseTickets)->where('ticket.estado', 'Abierto')->count();
        $ticketsEnProgreso = (clone $baseTickets)->where('ticket.estado', 'En Progreso')->count();
        $ticketsResueltos = (clone $baseTickets)->where('ticket.estado', 'Resuelto')->count();
        $ticketsCerrados = (clone $baseTickets)->where('ticket.estado', 'Cerrado')->count();

        // Diagnósticos
        $totalDiagnosticos = (clone $baseDiagnosticos)->count();
        $diagnosticosRecurrentes = (clone $baseDiagnosticos)->where('diagnostico.es_recurrente', 1)->count();

        // Configuraciones
        $totalConfiguraciones = (clone $baseConfiguraciones)->count();

        // Proyectos
        $totalProyectos = Proyecto::count();

        // SLA promedio
        $slaPromedio = Ticket::query()
            ->join('proyecto_ticket', 'ticket.id', '=', 'proyecto_ticket.ticket_id')
            ->join('proyecto', 'proyecto_ticket.proyecto_id', '=', 'proyecto.id')
            ->whereNotNull('ticket.fecha_resolucion')
            ->whereNotNull('ticket.fecha_cierre');

        if ($this->proyecto_id) {
            $slaPromedio->where('proyecto_ticket.proyecto_id', $this->proyecto_id);
        }
        if ($this->estado) {
            $slaPromedio->where('ticket.estado', $this->estado);
        }
        if ($this->fecha_inicio) {
            $slaPromedio->whereDate('ticket.fecha_creacion', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $slaPromedio->whereDate('ticket.fecha_creacion', '<=', $this->fecha_fin);
        }

        $slaPromedio = $slaPromedio->selectRaw('AVG(DATEDIFF(ticket.fecha_cierre, ticket.fecha_resolucion)) as sla_avg')
            ->value('sla_avg');

        // Tickets por proyecto (para gráfico)
        $ticketsPorProyecto = Ticket::query()
            ->select('proyecto.nombre', DB::raw('COUNT(*) as total'))
            ->join('proyecto_ticket', 'ticket.id', '=', 'proyecto_ticket.ticket_id')
            ->join('proyecto', 'proyecto_ticket.proyecto_id', '=', 'proyecto.id');

        if ($this->proyecto_id) {
            $ticketsPorProyecto->where('proyecto_ticket.proyecto_id', $this->proyecto_id);
        }
        if ($this->estado) {
            $ticketsPorProyecto->where('ticket.estado', $this->estado);
        }
        if ($this->fecha_inicio) {
            $ticketsPorProyecto->whereDate('ticket.fecha_creacion', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $ticketsPorProyecto->whereDate('ticket.fecha_creacion', '<=', $this->fecha_fin);
        }

        $ticketsPorProyecto = $ticketsPorProyecto
            ->groupBy('proyecto.id', 'proyecto.nombre')
            ->get();

        return [
            // Tickets
            'total_tickets' => $totalTickets,
            'tickets_abiertos' => $ticketsAbiertos,
            'tickets_en_progreso' => $ticketsEnProgreso,
            'tickets_resueltos' => $ticketsResueltos,
            'tickets_cerrados' => $ticketsCerrados,
            // Diagnósticos
            'total_diagnosticos' => $totalDiagnosticos,
            'diagnosticos_recurrentes' => $diagnosticosRecurrentes,
            // Configuraciones
            'total_configuraciones' => $totalConfiguraciones,
            // Proyectos
            'total_proyectos' => $totalProyectos,
            // SLA
            'sla_promedio' => round($slaPromedio ?? 0, 2),
            // Tickets por proyecto
            'tickets_por_proyecto' => $ticketsPorProyecto,
        ];
    }

    public function getTicketsDetalleProperty()
    {
        return $this->getTicketsQuery()
            ->orderBy('ticket.fecha_creacion', 'desc')
            ->limit(50)
            ->get();
    }

    public function getDiagnosticosDetalleProperty()
    {
        return $this->getDiagnosticosQuery()
            ->orderBy('diagnostico.id', 'desc')
            ->limit(50)
            ->get(['diagnostico.*']);
    }

    public function getConfiguracionesDetalleProperty()
    {
        return $this->getConfiguracionesQuery()
            ->orderBy('configuracion.id', 'desc')
            ->limit(50)
            ->get(['configuracion.*']);
    }

    public function getEventosRecientesProperty()
    {
        return Historial::orderBy('fecha', 'desc')->limit(10)->get();
    }

    // Filtros
    public function aplicarFiltros()
    {
        $this->proyecto_id = $this->filter_proyecto_id;
        $this->estado = $this->filter_estado;
        $this->fecha_inicio = $this->filter_fecha_inicio;
        $this->fecha_fin = $this->filter_fecha_fin;
    }

    public function limpiarFiltros()
    {
        $this->filter_proyecto_id = null;
        $this->filter_estado = null;
        $this->filter_fecha_inicio = null;
        $this->filter_fecha_fin = null;

        $this->proyecto_id = null;
        $this->estado = null;
        $this->fecha_inicio = null;
        $this->fecha_fin = null;
    }

    // Exportar tickets filtrados a CSV
    public function exportarTicketsCsv()
    {
        $tickets = $this->getTicketsQuery()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tickets.csv"',
        ];

        $callback = function() use ($tickets) {
            $handle = fopen('php://output', 'w');
            // Encabezados
            fputcsv($handle, ['ID', 'Título', 'Estado', 'Proyecto', 'Fecha creación']);
            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->id,
                    $ticket->titulo,
                    $ticket->estado,
                    $ticket->proyecto_nombre ?? '',
                    $ticket->fecha_creacion,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}