@php
    $contrato = $getRecord();

    // Horas asignadas desde recursos (pivot horas_asignadas) o total_horas
    $horasAsignadas = $contrato->recursos && $contrato->recursos->count()
        ? $contrato->recursos->sum(fn ($u) => (float) ($u->pivot->horas_asignadas ?? 0))
        : (float) ($contrato->total_horas ?? 0);

    $detalleClockify = $contrato->obtenerHorasClockify();

    $horasReportadas = (float) ($detalleClockify['total_horas'] ?? 0);
    $diferencia = $horasReportadas - $horasAsignadas;
    $porcentaje = $horasAsignadas > 0 ? round(($horasReportadas / $horasAsignadas) * 100, 2) : 0;

    $estado = 'sin_asignar';
    if ($horasAsignadas > 0) {
        if ($porcentaje >= 100) $estado = 'excedido';
        elseif ($porcentaje >= 90) $estado = 'proximo_limite';
        elseif ($porcentaje >= 50) $estado = 'en_progreso';
        else $estado = 'iniciando';
    }

    $estadoColor = match($estado) {
        'excedido' => 'red',
        'proximo_limite' => 'yellow',
        'en_progreso' => 'blue',
        'iniciando' => 'gray',
        'sin_asignar' => 'gray',
        default => 'gray',
    };

    $estadoLabel = match($estado) {
        'excedido' => 'Horas Excedidas',
        'proximo_limite' => 'Próximo al Límite',
        'en_progreso' => 'En Progreso',
        'iniciando' => 'Iniciando',
        'sin_asignar' => 'Sin Horas Asignadas',
        default => 'Desconocido',
    };
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
            <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Horas Asignadas</div>
            <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($horasAsignadas, 2) }}</div>
        </div>

        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
            <div class="text-sm text-green-600 dark:text-green-400 font-medium">Horas Reportadas</div>
            <div class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($horasReportadas, 2) }}</div>
        </div>

        <div class="p-4 bg-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-50 dark:bg-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-900/20 rounded-lg border border-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-200 dark:border-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-700">
            <div class="text-sm text-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-600 dark:text-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-400 font-medium">
                {{ $diferencia >= 0 ? 'Horas de Más' : 'Horas Faltantes' }}
            </div>
            <div class="text-2xl font-bold text-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-900 dark:text-{{ $diferencia >= 0 ? 'orange' : 'purple' }}-100">
                {{ number_format(abs($diferencia), 2) }}
            </div>
        </div>

        <div class="p-4 bg-{{$estadoColor}}-50 dark:bg-{{$estadoColor}}-900/20 rounded-lg border border-{{$estadoColor}}-200 dark:border-{{$estadoColor}}-700">
            <div class="text-sm text-{{$estadoColor}}-600 dark:text-{{$estadoColor}}-400 font-medium">Estado</div>
            <div class="text-lg font-bold text-{{$estadoColor}}-900 dark:text-{{$estadoColor}}-100">{{ $estadoLabel }}</div>
            <div class="text-sm text-{{$estadoColor}}-600 dark:text-{{$estadoColor}}-400">{{ $porcentaje }}%</div>
        </div>
    </div>

    <div class="mt-4">
        <div class="flex justify-between text-sm mb-1">
            <span class="font-medium">Progreso de Horas</span>
            <span>{{ $porcentaje }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div class="bg-{{$estadoColor}}-600 h-4 rounded-full" style="width: {{ min($porcentaje, 100) }}%"></div>
        </div>
    </div>

    @if($estado === 'excedido')
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300">
            <p class="font-bold">⚠️ Alerta: Horas Excedidas</p>
            <p class="text-sm">Se han reportado {{ number_format(abs($diferencia), 2) }} horas más de las asignadas.</p>
        </div>
    @elseif($estado === 'proximo_limite')
        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-300">
            <p class="font-bold">⚠️ Advertencia: Próximo al Límite</p>
            <p class="text-sm">Quedan {{ number_format(max($horasAsignadas - $horasReportadas, 0), 2) }} horas disponibles.</p>
        </div>
    @endif

    @if(!empty($detalleClockify['detalle_usuarios']))
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-3">Detalle de Horas por Colaborador</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Colaborador</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas Reportadas</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Entradas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($detalleClockify['detalle_usuarios'] as $usuario)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $usuario['usuario'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $usuario['email'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($usuario['horas'], 2) }}h</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ $usuario['entradas'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">Total</td>
                            <td class="px-4 py-3 text-sm font-bold text-right text-gray-900 dark:text-gray-100">{{ number_format($detalleClockify['total_horas'] ?? 0, 2) }}h</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">{{ collect($detalleClockify['detalle_usuarios'])->sum('entradas') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- Entradas detalladas --}}
    @if(!empty($detalleClockify['entradas']))
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-3">Entradas de tiempo</h3>
            <div class="space-y-2">
                @foreach($detalleClockify['entradas'] as $e)
                    <div class="p-3 rounded border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <div class="flex justify-between gap-2 text-sm">
                            <div>
                                <span class="font-semibold">{{ $e['usuario'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">({{ $e['email'] }})</span>
                            </div>
                            <div class="text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($e['start'])->format('d/m/Y H:i') }}
                                @if(!empty($e['end'])) - {{ \Carbon\Carbon::parse($e['end'])->format('d/m/Y H:i') }} @endif
                                • <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($e['duration_hours'], 2) }}h</span>
                            </div>
                        </div>
                        @if(!empty($e['description']))
                            <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $e['description'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($detalleClockify['error']))
        <div class="p-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Proyecto en Clockify:</strong> {{ $detalleClockify['error'] }}</p>
        </div>
    @elseif(isset($detalleClockify['proyecto_clockify']))
        <div class="p-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Proyecto en Clockify:</strong> {{ $detalleClockify['proyecto_clockify'] }}</p>
        </div>
    @endif
</div>