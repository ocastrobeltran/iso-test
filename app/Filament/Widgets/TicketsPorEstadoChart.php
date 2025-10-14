<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;

class TicketsPorEstadoChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets por Estado';

    protected function getData(): array
    {
        $data = Ticket::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    // public static function canView(): bool
    // {
    //     return request()->routeIs('filament.pages.reporte-dashboard');
    // }
}