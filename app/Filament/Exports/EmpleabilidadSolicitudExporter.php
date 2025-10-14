<?php

namespace App\Filament\Exports;

use App\Models\EmpleabilidadSolicitud;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EmpleabilidadSolicitudExporter extends Exporter
{
    protected static ?string $model = EmpleabilidadSolicitud::class;

    public function getFormats(): array
    {
        return [
            'csv',
        ];
    }

    // Configurar para descarga inmediata
    public function getJobQueue(): ?string
    {
        return null; // Esto hace que sea síncrono
    }

    public function getJobConnection(): ?string
    {
        return null; // Esto hace que sea síncrono
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('tipo_solicitud')
                ->label('Tipo de Solicitud'),
            ExportColumn::make('herramienta')
                ->label('Herramienta'),
            ExportColumn::make('canal')
                ->label('Canal'),
            ExportColumn::make('quien_solicito')
                ->label('Quién lo Solicitó'),
            ExportColumn::make('descripcion_requerimiento')
                ->label('Descripción del Requerimiento'),
            ExportColumn::make('solucion')
                ->label('Solución'),
            ExportColumn::make('landing')
                ->label('Landing'),
            ExportColumn::make('responsable_ejecucion')
                ->label('Responsable de Ejecución'),
            ExportColumn::make('fecha_inicio')
                ->label('Fecha de Inicio'),
            ExportColumn::make('fecha_creacion')
                ->label('Fecha de Creación'),
            ExportColumn::make('mes')
                ->label('Mes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Archivo CSV descargado exitosamente.';
    }
}