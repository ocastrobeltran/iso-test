<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\ClockifyService;
use Illuminate\Support\Carbon;

/**
 * Class Contrato
 * 
 * @property int $id
 * @property int|null $total_horas
 * @property string|null $estado
 * @property int|null $cliente_id
 * 
 * @property Usuario|null $usuario
 * @property Collection|Proveedor[] $proveedors
 * @property Collection|Proyecto[] $proyectos
 *
 * @package App\Models
 */
class Contrato extends Model
{
	protected $table = 'contrato';
	public $timestamps = false;

	protected $casts = [
		'total_horas' => 'int',
		'cliente_id' => 'int'
	];

	protected $fillable = [
        'titulo',
        'cotizacion',
        'valor',
        'etapa',
        'estado_factura',
        'cliente_id',
    ];

	public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function proveedors()
    {
        return $this->belongsToMany(Proveedor::class, 'contrato_proveedor', 'contrato_id', 'proveedor_id');
    }

    public function recursos()
    {
        return $this->belongsToMany(\App\Models\User::class, 'contrato_recurso', 'contrato_id', 'user_id')
            ->withPivot('horas_asignadas');
    }

    public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }

    public function proyectos()
    {
        return $this->belongsToMany(\App\Models\Proyecto::class, 'proyecto_contrato', 'contrato_id', 'proyecto_id');
    }

    public function fees()
    {
        return $this->belongsToMany(\App\Models\Fee::class, 'fee_contrato', 'contrato_id', 'fee_id');
    }

    public function calcularHorasReportadas(): array
    {
        $totalHorasAsignadas = $this->recursos->sum('horas_asignadas') ?? 0;
        $totalHorasReportadas = $this->obtenerHorasClockify()['total_horas'] ?? 0;

        return [
            'asignadas' => $totalHorasAsignadas,
            'reportadas' => $totalHorasReportadas,
            'diferencia' => $totalHorasReportadas - $totalHorasAsignadas,
            'porcentaje_cumplimiento' => $totalHorasAsignadas > 0 
                ? round(($totalHorasReportadas / $totalHorasAsignadas) * 100, 2) 
                : 0,
            'estado' => $this->determinarEstadoHoras($totalHorasReportadas, $totalHorasAsignadas),
        ];
    }

    /**
     * Obtiene las horas reportadas en Clockify para este contrato
     */
    public function obtenerHorasClockify(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $clockify = app(ClockifyService::class);
        $projectName = trim(($this->cotizacion ?? '') . ' - ' . ($this->titulo ?? ''));

        try {
            $project = $clockify->findProjectByName($projectName);

            if (!$project) {
                return [
                    'total_horas' => 0,
                    'detalle_usuarios' => [],
                    'entradas' => [],
                    'error' => 'Proyecto no encontrado en Clockify',
                ];
            }

            $start = $fechaInicio ? Carbon::parse($fechaInicio)->toIso8601String() : now()->subMonths(12)->startOfDay()->toIso8601String();
            $end = $fechaFin ? Carbon::parse($fechaFin)->toIso8601String() : now()->endOfDay()->toIso8601String();

            $users = $clockify->getUsers();

            $detalleUsuarios = [];
            $entradas = [];
            $totalSeconds = 0;

            foreach ($users as $u) {
                $userId = (string) ($u['id'] ?? '');
                if ($userId === '') {
                    continue;
                }

                $userEntries = $clockify->getUserTimeEntries($userId, $start, $end, $project['id']);
                if (empty($userEntries)) {
                    continue;
                }

                $userSeconds = 0;

                foreach ($userEntries as $entry) {
                    $durIso = data_get($entry, 'timeInterval.duration', 'PT0S');
                    $seconds = $this->iso8601ToSeconds($durIso);
                    $userSeconds += $seconds;
                    $totalSeconds += $seconds;

                    $entradas[] = [
                        'id' => $entry['id'] ?? null,
                        'usuario' => $u['name'] ?? 'Desconocido',
                        'email' => $u['email'] ?? '',
                        'description' => $entry['description'] ?? '',
                        'start' => data_get($entry, 'timeInterval.start'),
                        'end' => data_get($entry, 'timeInterval.end'),
                        'duration_hours' => round($seconds / 3600, 2),
                    ];
                }

                $detalleUsuarios[] = [
                    'usuario' => $u['name'] ?? 'Desconocido',
                    'email' => $u['email'] ?? '',
                    'horas' => round($userSeconds / 3600, 2),
                    'entradas' => count($userEntries),
                ];
            }

            // Ordenar entradas por fecha descendente
            usort($entradas, fn ($a, $b) => strcmp((string) ($b['start'] ?? ''), (string) ($a['start'] ?? '')));

            return [
                'total_horas' => round($totalSeconds / 3600, 2),
                'detalle_usuarios' => $detalleUsuarios,
                'entradas' => $entradas,
                'proyecto_clockify' => $project['name'],
                'proyecto_id' => $project['id'],
            ];
        } catch (\Throwable $e) {
            return [
                'total_horas' => 0,
                'detalle_usuarios' => [],
                'entradas' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Determina el estado de las horas según el cumplimiento
     */
    protected function determinarEstadoHoras(float $reportadas, float $asignadas): string
    {
        if ($asignadas == 0) return 'sin_asignar';
        
        $porcentaje = ($reportadas / $asignadas) * 100;

        if ($porcentaje >= 100) return 'excedido';
        if ($porcentaje >= 90) return 'proximo_limite';
        if ($porcentaje >= 50) return 'en_progreso';
        
        return 'iniciando';
    }

    /**
     * Convierte duración ISO8601 a segundos
     */
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

    public function horasAsignadas(): float
    {
        // Suma de horas en el pivot 'horas_asignadas'. Fallback a total_horas si existe.
        if ($this->relationLoaded('recursos') || $this->recursos()->exists()) {
            return (float) ($this->recursos?->sum(fn ($u) => (float) ($u->pivot->horas_asignadas ?? 0)) ?? 0);
        }

        return (float) ($this->total_horas ?? 0);
    }
}
