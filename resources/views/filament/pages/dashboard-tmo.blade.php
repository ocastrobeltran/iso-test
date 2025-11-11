{{-- filepath: resources/views/filament/pages/dashboard-tmo.blade.php --}}
<x-filament-panels::page>

    <style>
        /* Hace visible el ícono del selector de fecha en dark mode */
        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) opacity(.8);
        }
    </style>
    
    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Filtros</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                    <input
                        type="date"
                        wire:model.live="fecha_inicio"
                        placeholder="aaaa-mm-dd"
                        class="w-full rounded-lg
                               bg-white dark:bg-gray-900
                               text-gray-900 dark:text-gray-100
                               placeholder-gray-500 dark:placeholder-gray-400
                               border border-gray-300 dark:border-gray-700
                               focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                               selection:bg-primary-600 selection:text-white"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha Fin</label>
                    <input
                        type="date"
                        wire:model.live="fecha_fin"
                        placeholder="aaaa-mm-dd"
                        class="w-full rounded-lg
                               bg-white dark:bg-gray-900
                               text-gray-900 dark:text-gray-100
                               placeholder-gray-500 dark:placeholder-gray-400
                               border border-gray-300 dark:border-gray-700
                               focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                               selection:bg-primary-600 selection:text-white"
                    />
                </div>
            </div>
        </div>

        {{-- KPIs Portafolio --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-briefcase class="w-6 h-6 mr-2" />
                Portafolio Global
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Proyectos</div>
                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $kpis_portafolio['total_proyectos'] }}</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200">
                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">Proyectos Activos</div>
                    <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $kpis_portafolio['proyectos_activos'] }}</div>
                </div>
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200">
                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">Total Contratos</div>
                    <div class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $kpis_portafolio['total_contratos'] }}</div>
                </div>
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Revenue Fees Mensual</div>
                    <div class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">${{ number_format($kpis_portafolio['revenue_fees_mensual'], 0) }}</div>
                </div>
            </div>
        </div>

        {{-- Satisfacción del Cliente (NPS) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-face-smile class="w-6 h-6 mr-2" />
                Satisfacción del Cliente (NPS)
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200">
                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">NPS Promedio</div>
                    <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $satisfaccion['nps_promedio'] }}</div>
                </div>
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Promotores (9-10)</div>
                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $satisfaccion['promotores'] }}</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Pasivos (7-8)</div>
                    <div class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ $satisfaccion['pasivos'] }}</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200">
                    <div class="text-sm text-red-600 dark:text-red-400 font-medium">Detractores (0-6)</div>
                    <div class="text-3xl font-bold text-red-900 dark:text-red-100">{{ $satisfaccion['detractores'] }}</div>
                </div>
            </div>
        </div>

        {{-- Eficiencia (SPI, Horas, Documentación) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-chart-pie class="w-6 h-6 mr-2" />
                Eficiencia Global
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200">
                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">SPI Promedio</div>
                    <div class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $eficiencia['spi_promedio'] }}</div>
                    <div class="text-xs text-purple-500 mt-1">{{ $eficiencia['spi_promedio'] >= 1 ? '✓ Adelantado' : '⚠ Retrasado' }}</div>
                </div>
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Horas Estimadas</div>
                    <div class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">{{ number_format($eficiencia['total_horas_estimadas'], 0) }}</div>
                </div>
                <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-200">
                    <div class="text-sm text-teal-600 dark:text-teal-400 font-medium">Horas Ejecutadas</div>
                    <div class="text-3xl font-bold text-teal-900 dark:text-teal-100">{{ number_format($eficiencia['total_horas_ejecutadas'], 0) }}</div>
                </div>
                <div class="p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200">
                    <div class="text-sm text-cyan-600 dark:text-cyan-400 font-medium">Documentación Promedio</div>
                    <div class="text-3xl font-bold text-cyan-900 dark:text-cyan-100">{{ $eficiencia['documentacion_promedio'] }}%</div>
                </div>
            </div>
        </div>

        {{-- Incidentes (Tickets) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 mr-2" />
                Incidentes (Tickets)
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200">
                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Total</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $incidentes['total_tickets'] }}</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200">
                    <div class="text-sm text-red-600 dark:text-red-400 font-medium">Abiertos</div>
                    <div class="text-3xl font-bold text-red-900 dark:text-red-100">{{ $incidentes['tickets_abiertos'] }}</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">En Progreso</div>
                    <div class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ $incidentes['tickets_en_progreso'] }}</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200">
                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">Resueltos</div>
                    <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $incidentes['tickets_resueltos'] }}</div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200">
                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Tiempo Prom. (h)</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ round($incidentes['tiempo_resolucion_promedio'] ?? 0, 1) }}</div>
                </div>
            </div>
        </div>

        {{-- Oportunidades de Mejora --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-light-bulb class="w-6 h-6 mr-2" />
                Oportunidades de Mejora
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Mejoras</div>
                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $oportunidades['total_mejoras'] }}</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200">
                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">Implementadas</div>
                    <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $oportunidades['mejoras_implementadas'] }}</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Pendientes</div>
                    <div class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ $oportunidades['mejoras_pendientes'] }}</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200">
                    <div class="text-sm text-red-600 dark:text-red-400 font-medium">Rechazadas</div>
                    <div class="text-3xl font-bold text-red-900 dark:text-red-100">{{ $oportunidades['mejoras_rechazadas'] }}</div>
                </div>
            </div>
        </div>

        {{-- Riesgos y Fases --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <x-heroicon-o-shield-exclamation class="w-6 h-6 mr-2" />
                    Gestión de Riesgos
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Identificados</span>
                        <span class="text-2xl font-bold">{{ $riesgos_fases['riesgos_identificados'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Mitigados</span>
                        <span class="text-2xl font-bold">{{ $riesgos_fases['riesgos_mitigados'] }}</span>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Porcentaje de Mitigación</span>
                            <span class="font-semibold">{{ $riesgos_fases['porcentaje_mitigacion'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                            <div class="bg-green-600 h-4 rounded-full" style="width: {{ $riesgos_fases['porcentaje_mitigacion'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <x-heroicon-o-clipboard-document-check class="w-6 h-6 mr-2" />
                    Cumplimiento de Fases
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Planeadas</span>
                        <span class="text-2xl font-bold">{{ $riesgos_fases['fases_planeadas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Entregadas</span>
                        <span class="text-2xl font-bold">{{ $riesgos_fases['fases_entregadas'] }}</span>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Porcentaje de Cumplimiento</span>
                            <span class="font-semibold">{{ $riesgos_fases['porcentaje_cumplimiento_fases'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $riesgos_fases['porcentaje_cumplimiento_fases'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 Proyectos por Avance --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-trophy class="w-6 h-6 mr-2" />
                Top 5 Proyectos por Avance
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Proyecto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avance Real</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">PM</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($top_proyectos as $index => $proyecto)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ url("/admin/resources/proyectos/{$proyecto->id}/view") }}" class="filament-link">{{ $proyecto->nombre }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $proyecto->estado === 'En ejecución' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $proyecto->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $proyecto->porcentaje_avance_real }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $proyecto->pmResponsable?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Proyectos con Alertas --}}
        @if($proyectos_alertas->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg shadow border-l-4 border-red-500">
                <h3 class="text-xl font-bold mb-4 flex items-center text-red-700 dark:text-red-300">
                    <x-heroicon-o-exclamation-circle class="w-6 h-6 mr-2" />
                    Proyectos con Alertas ({{ $proyectos_alertas->count() }})
                </h3>
                <div class="space-y-2">
                    @foreach($proyectos_alertas as $proyecto)
                        <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded">
                            <div>
                                <a href="{{ url("/admin/resources/proyectos/{$proyecto->id}/view") }}" class="font-semibold filament-link">{{ $proyecto->nombre }}</a>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    @if($proyecto->porcentaje_avance_real < $proyecto->porcentaje_avance_planeado * 0.9)
                                        <span class="text-red-600">⚠ Retraso en avance</span>
                                    @endif
                                    @if($proyecto->horas_ejecutadas > $proyecto->horas_estimadas * 1.1)
                                        <span class="text-orange-600">⚠ Sobre-consumo de horas</span>
                                    @endif
                                    @if($proyecto->riesgos_mitigados < $proyecto->riesgos_identificados * 0.5)
                                        <span class="text-yellow-600">⚠ Riesgos altos</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">{{ $proyecto->pmResponsable?->name ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>