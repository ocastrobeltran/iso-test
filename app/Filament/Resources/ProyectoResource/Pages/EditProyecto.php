<?php

namespace App\Filament\Resources\ProyectoResource\Pages;

use App\Filament\Resources\ProyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Filament\Forms; 

class EditProyecto extends EditRecord
{
    protected static string $resource = ProyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('finalizar')
                ->visible(fn ($record) => strtolower(trim($record->estado)) !== 'finalizado' && strtolower(trim($record->estado)) !== 'cancelado')
                ->label('Finalizar Proyecto')
                ->color('danger')
                ->icon('heroicon-o-check-badge')
                ->modalHeading('Cierre / Finalización de Proyecto')
                ->modalWidth('4xl')
                // Pre-cargar valores desde el proyecto
                ->mountUsing(function (Forms\ComponentContainer $form) {
                    $pm = $this->record?->pmResponsable;
                    $form->fill([
                        'responsable_nombre' => $pm?->name,
                        'responsable_cargo' => $pm?->rol,
                        'periodo'            => Carbon::now()->toDateString(),
                        'resumen_general'    => $this->record?->descripcion,
                    ]);
                })
                ->form([
                    \Filament\Forms\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\TextInput::make('responsable_nombre')
                            ->label('Responsable')
                            ->required()
                            ->disabled()
                            ->dehydrated(true),
                        \Filament\Forms\Components\TextInput::make('responsable_cargo')
                            ->label('Cargo')
                            ->required()
                            ->disabled()
                            ->dehydrated(true),
                        \Filament\Forms\Components\DatePicker::make('periodo')
                            ->label('Periodo (mes de cierre / fecha fin)')
                            ->native(false)
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('resumen_general')
                            ->label('Resumen general')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                    \Filament\Forms\Components\Fieldset::make('KPIs del periodo')->schema([
                        \Filament\Forms\Components\Placeholder::make('kpi_desviacion_tiempo_pct')
                            ->label('Desviación de Tiempo (%)'),
                        \Filament\Forms\Components\Placeholder::make('kpi_cumplimiento_cronograma_pct')
                            ->label('Cumplimiento de cronograma (%)'),
                        \Filament\Forms\Components\Placeholder::make('kpi_roi_pct')
                            ->label('ROI (%)'),
                        \Filament\Forms\Components\Placeholder::make('kpi_documentacion_completa_pct')
                            ->label('Documentación completa (%)'),
                        \Filament\Forms\Components\TextInput::make('kpi_nps')
                            ->label('NPS')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10),
                    ])->columns(2),
                    \Filament\Forms\Components\Textarea::make('riesgos_identificados')
                        ->label('Riesgos identificados')
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('oportunidades_mejora')
                        ->label('Oportunidades de mejora')
                        ->required()
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('lecciones_aprendidas')
                        ->label('Lecciones aprendidas')
                        ->required()
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('acciones_correctivas')
                        ->label('Acciones correctivas / preventivas')
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('recomendaciones_cierre')
                        ->label('Recomendaciones al cierre')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    // Crea registro de cierre (asume tabla proyecto_cierres creada)
                    $this->record->cierre()->create([
                        'periodo' => $data['periodo'] ?? Carbon::now()->toDateString(),
                        'responsable_nombre' => $data['responsable_nombre'] ?? ($pm?->name ?? null),
                        'responsable_cargo' => $data['responsable_cargo'] ?? ($pm?->rol ?? null),
                        'resumen_general' => $data['resumen_general'] ?? ($this->record?->descripcion ?? null),
                        'kpi_desviacion_tiempo_pct' => (float) $this->calcDesviacionTiempo(),
                        'kpi_cumplimiento_cronograma_pct' => (float) $this->calcCumplimientoCronograma(),
                        'kpi_roi_pct' => (float) $this->calcRoi(),
                        'kpi_documentacion_completa_pct' => (float) ($this->record->avance_documentacion ?? 0),
                        'kpi_nps' => $data['kpi_nps'] ?? null,
                        'riesgos_identificados' => $data['riesgos_identificados'] ?? null,
                        'oportunidades_mejora' => $data['oportunidades_mejora'],
                        'lecciones_aprendidas' => $data['lecciones_aprendidas'],
                        'acciones_correctivas' => $data['acciones_correctivas'] ?? null,
                        'recomendaciones_cierre' => $data['recomendaciones_cierre'],
                    ]);

                    // Marca proyecto como finalizado
                    $this->record->estado = 'Finalizado';
                    $this->record->fecha_fin_real = $this->record->fecha_fin_real ?: now();
                    $this->record->save();

                    Notification::make()->title('Proyecto finalizado')->success()->send();
                })
                ->modalSubmitActionLabel('Guardar y finalizar'),
        ];
    }

    protected function afterSave(): void
    {
        // Ya no necesitas sincronizar contratos, solo servicios si aplica
        $servicios = $this->data['servicios'] ?? [];
        $this->record->servicios()->sync($servicios);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            isset($data['fecha_inicio'], $data['fecha_fin']) &&
            $data['fecha_inicio'] > $data['fecha_fin']
        ) {
            Notification::make()
                ->title('Error en las fechas')
                ->body('La fecha de inicio no puede ser mayor que la fecha de fin.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'fecha_inicio' => 'La fecha de inicio no puede ser mayor que la fecha de fin.',
                'fecha_fin' => 'La fecha de fin no puede ser menor que la fecha de inicio.',
            ]);
        }
        return $data;
    }

    protected function afterFill(): void
    {
        if ($this->record && $this->record->estado === 'Finalizado' && !$this->record->cierre) {
            \Filament\Notifications\Notification::make()
                ->title('Cierre pendiente')
                ->body('El proyecto está marcado como Finalizado sin formulario de cierre. Complete el cierre con el botón “Finalizar Proyecto”.')
                ->danger()
                ->persistent()
                ->send();
        }
    }

    // KPIs (basados en horas estimadas/ejecutadas)
    protected function calcDesviacionTiempo(): string
    {
        $est = (float) ($this->record->horas_estimadas ?? 0);
        $real = (float) ($this->record->horas_ejecutadas ?? 0);
        if ($est <= 0) return '0.00';
        return number_format((($real - $est) / $est) * 100, 2, '.', '');
    }

    protected function calcCumplimientoCronograma(): string
    {
        $est = (float) ($this->record->horas_estimadas ?? 0);
        $real = (float) ($this->record->horas_ejecutadas ?? 0);
        if ($real <= 0) return '0.00';
        return number_format(($est / $real) * 100, 2, '.', '');
    }

    protected function calcRoi(): string
    {
        $est = (float) ($this->record->horas_estimadas ?? 0);
        $real = (float) ($this->record->horas_ejecutadas ?? 0);
        if ($est <= 0) return '0.00';
        return number_format((($est - $real) / $est) * 100, 2, '.', '');
    }
}