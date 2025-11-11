<x-filament-panels::page>
    <style>
        .dark input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(.8); }
        .dark select { background-color: rgb(17 24 39); color: rgb(243 244 246); }
        .dark option { background-color: rgb(17 24 39); color: rgb(243 244 246); }
    </style>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Filtros</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                    <input
                        type="date"
                        wire:model.live="fecha_inicio"
                        class="w-full rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha Fin</label>
                    <input
                        type="date"
                        wire:model.live="fecha_fin"
                        class="w-full rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Recurso</label>
                    <select
                        wire:model.live="recurso_id"
                        class="w-full rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="">Todos</option>
                        @foreach($recursosOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Totales Rápidos --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-chart-bar-square class="w-6 h-6 mr-2" />
                Resumen Global
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Recursos</div>
                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $data['totales']['recursos'] }}</div>
                </div>
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Horas Asignadas</div>
                    <div class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">{{ number_format($data['totales']['horas_asignadas'], 0) }}</div>
                </div>
                <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-200">
                    <div class="text-sm text-teal-600 dark:text-teal-400 font-medium">Horas Reportadas</div>
                    <div class="text-3xl font-bold text-teal-900 dark:text-teal-100">{{ number_format($data['totales']['horas_reportadas'], 0) }}</div>
                </div>
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200">
                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">Utilización Promedio</div>
                    <div class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $data['totales']['utilizacion_promedio'] }}%</div>
                </div>
            </div>
        </div>

        {{-- Tabla de Recursos --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-users class="w-6 h-6 mr-2" />
                Carga por Recurso
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Recurso</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Capacidad</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Asignadas</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reportadas</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Disponibles</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Utilización</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cumplimiento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($data['lista'] as $r)
                            @php
                                $estadoBg = match($r['estado']) {
                                    'sobreutilizado' => 'bg-red-100 dark:bg-red-900/30',
                                    'subutilizado' => 'bg-yellow-100 dark:bg-yellow-900/30',
                                    default => 'bg-green-100 dark:bg-green-900/30',
                                };
                                $estadoText = match($r['estado']) {
                                    'sobreutilizado' => 'text-red-800 dark:text-red-300',
                                    'subutilizado' => 'text-yellow-800 dark:text-yellow-300',
                                    default => 'text-green-800 dark:text-green-300',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/60">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $r['nombre'] }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $r['email'] }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ $r['capacidad_estandar'] }}h</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                    {{ number_format($r['horas_asignadas_total'], 1) }}h
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        C: {{ number_format($r['horas_asignadas_contratos'], 1) }} | P: {{ number_format($r['horas_asignadas_proyectos'], 1) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($r['horas_reportadas'], 1) }}h</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-gray-300">{{ number_format($r['horas_disponibles'], 1) }}h</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="w-48">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $r['utilizacion'] }}%</span>
                                            <span class="font-semibold">{{ $r['capacidad_estandar'] }}h std.</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full {{ $r['utilizacion'] >= 110 ? 'bg-red-600' : ($r['utilizacion'] <= 70 ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                                                 style="width: {{ min($r['utilizacion'], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="w-40">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $r['cumplimiento'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full bg-sky-600" style="width: {{ min($r['cumplimiento'], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $estadoBg }} {{ $estadoText }}">
                                        {{ ucfirst($r['estado']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($data['lista']))
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No se encontraron recursos con los filtros seleccionados.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Redistribución Sugerida --}}
        @if(!empty($data['redistribucion']))
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <x-heroicon-o-arrows-right-left class="w-6 h-6 mr-2" />
                    Redistribución Sugerida
                </h3>
                <div class="space-y-2">
                    @foreach($data['redistribucion'] as $s)
                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $s['desde'] }}</span>
                                <x-heroicon-o-arrow-right class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $s['hacia'] }}</span>
                            </div>
                            <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm font-semibold">
                                {{ number_format($s['horas'], 1) }}h
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-700">
                <p class="text-sm text-green-700 dark:text-green-300 flex items-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                    No se requiere redistribución. La carga está balanceada.
                </p>
            </div>
        @endif

        {{-- Leyenda --}}
        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h4 class="font-semibold mb-2 text-sm text-gray-700 dark:text-gray-300">Leyenda</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-600"></div>
                    <span><strong>Sobreutilizado:</strong> Utilización ≥ 110%</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-600"></div>
                    <span><strong>Óptimo:</strong> Utilización 70-110%</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <span><strong>Subutilizado:</strong> Utilización &lt; 70%</span>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                <strong>Capacidad estándar:</strong> 160h/mes (aproximado). <strong>Utilización:</strong> (Reportadas / Capacidad) × 100. <strong>Cumplimiento:</strong> (Reportadas / Asignadas) × 100.
            </div>
        </div>
    </div>
</x-filament-panels::page>