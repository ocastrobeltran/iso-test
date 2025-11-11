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
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">PM</label>
                    <select
                        wire:model.live="pm_id"
                        class="w-full rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="">Todos</option>
                        @foreach($pmOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Totales rápidos --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-presentation-chart-line class="w-6 h-6 mr-2" />
                Resumen General
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Proyectos</div>
                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $data['totales']['proyectos'] }}</div>
                </div>
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Horas Est.</div>
                    <div class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">{{ number_format($data['totales']['horas_estimadas'], 0) }}</div>
                </div>
                <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-200">
                    <div class="text-sm text-teal-600 dark:text-teal-400 font-medium">Horas Ejec.</div>
                    <div class="text-2xl font-bold text-teal-900 dark:text-teal-100">{{ number_format($data['totales']['horas_ejecutadas'], 0) }}</div>
                </div>
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200">
                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">SPI Prom.</div>
                    <div class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $data['totales']['prom_spi'] }}</div>
                </div>
                <div class="p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200">
                    <div class="text-sm text-cyan-600 dark:text-cyan-400 font-medium">Doc. Prom.</div>
                    <div class="text-3xl font-bold text-cyan-900 dark:text-cyan-100">{{ $data['totales']['prom_doc'] }}%</div>
                </div>
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200">
                    <div class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">NPS Prom.</div>
                    <div class="text-3xl font-bold text-emerald-900 dark:text-emerald-100">{{ $data['totales']['prom_nps'] }}</div>
                </div>
            </div>
        </div>

        {{-- Tabla por PM --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-user-group class="w-6 h-6 mr-2" />
                Desempeño por PM
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">PM</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Proyectos</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas Est.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas Ejec.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Consumo</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SPI</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NPS</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Documentación</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fases</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Riesgos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($data['lista'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/60">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $row['pm'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ $row['proyectos'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['horas_estimadas'], 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['horas_ejecutadas'], 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="w-48">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $row['porcentaje_horas'] }}%</span>
                                            <span>{{ max($row['horas_estimadas'] - $row['horas_ejecutadas'], 0) }}h disp.</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full {{ $row['porcentaje_horas'] >= 100 ? 'bg-red-600' : ($row['porcentaje_horas'] >= 90 ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                                                 style="width: {{ min($row['porcentaje_horas'], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold {{ $row['spi'] >= 1 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $row['spi'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ $row['nps'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="w-40">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $row['doc'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full bg-sky-600" style="width: {{ min($row['doc'], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="w-40">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $row['fases_entregadas'] }}/{{ $row['fases_planeadas'] }}</span>
                                            <span>{{ $row['cumplimiento_fases'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full bg-indigo-600" style="width: {{ min($row['cumplimiento_fases'], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="w-40">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $row['riesgos_mitigados'] }}/{{ $row['riesgos_identificados'] }}</span>
                                            <span>{{ $row['mitigacion'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full bg-emerald-600" style="width: {{ min($row['mitigacion'], 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($data['lista']))
                            <tr>
                                <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No se encontraron datos con los filtros seleccionados.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rankings --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-3">Top SPI</h3>
                <ul class="space-y-2">
                    @foreach($data['ranking_spi'] as $r)
                        <li class="flex justify-between text-sm">
                            <span class="text-gray-900 dark:text-gray-100">{{ $r['pm'] }}</span>
                            <span class="font-semibold {{ $r['spi'] >= 1 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $r['spi'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-3">Mayor consumo de horas</h3>
                <ul class="space-y-2">
                    @foreach($data['ranking_horas'] as $r)
                        <li class="flex justify-between text-sm">
                            <span class="text-gray-900 dark:text-gray-100">{{ $r['pm'] }}</span>
                            <span class="font-semibold {{ $r['porcentaje_horas'] >= 100 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">{{ $r['porcentaje_horas'] }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-3">Mejor documentación</h3>
                <ul class="space-y-2">
                    @foreach($data['ranking_doc'] as $r)
                        <li class="flex justify-between text-sm">
                            <span class="text-gray-900 dark:text-gray-100">{{ $r['pm'] }}</span>
                            <span class="font-semibold text-sky-600 dark:text-sky-400">{{ $r['doc'] }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-filament-panels::page>