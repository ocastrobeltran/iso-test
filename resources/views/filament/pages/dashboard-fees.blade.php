{{-- filepath: resources/views/filament/pages/dashboard-fees.blade.php --}}
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Estado</label>
                    <select
                        wire:model.live="estado"
                        class="w-full rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="">Todos</option>
                        @foreach($estadosOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Cliente</label>
                    <select
                        wire:model.live="cliente_id"
                        class="w-full rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="">Todos</option>
                        @foreach($clientesOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- KPIs Principales --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-currency-dollar class="w-6 h-6 mr-2" />
                Resumen de Fees
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Fees</div>
                    <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $data['totales']['total_fees'] }}</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200">
                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">Fees Activos</div>
                    <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $data['totales']['fees_activos'] }}</div>
                </div>
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Revenue Mensual</div>
                    <div class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">${{ number_format($data['totales']['revenue_mensual'], 0) }}</div>
                </div>
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200">
                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">Revenue Anual</div>
                    <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">${{ number_format($data['totales']['revenue_anual'], 0) }}</div>
                </div>
            </div>
        </div>

        {{-- Consumo de Horas --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-clock class="w-6 h-6 mr-2" />
                Consumo de Horas
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-200">
                    <div class="text-sm text-teal-600 dark:text-teal-400 font-medium">Horas Disponibles</div>
                    <div class="text-3xl font-bold text-teal-900 dark:text-teal-100">{{ number_format($data['totales']['horas_disponibles'], 0) }}h</div>
                </div>
                <div class="p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200">
                    <div class="text-sm text-cyan-600 dark:text-cyan-400 font-medium">Horas Consumidas</div>
                    <div class="text-3xl font-bold text-cyan-900 dark:text-cyan-100">{{ number_format($data['totales']['horas_consumidas'], 0) }}h</div>
                </div>
                <div class="p-4 bg-sky-50 dark:bg-sky-900/20 rounded-lg border border-sky-200">
                    <div class="text-sm text-sky-600 dark:text-sky-400 font-medium mb-2">Porcentaje de Consumo</div>
                    <div class="text-3xl font-bold text-sky-900 dark:text-sky-100 mb-2">{{ $data['totales']['porcentaje_consumo'] }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                        <div class="h-3 rounded-full {{ $data['totales']['porcentaje_consumo'] >= 90 ? 'bg-red-600' : ($data['totales']['porcentaje_consumo'] >= 70 ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                             style="width: {{ min($data['totales']['porcentaje_consumo'], 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertas de Renovación --}}
        @if($data['alertas']['proximos_vencer']->count() > 0 || $data['alertas']['vencidos']->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Próximos a Vencer --}}
                @if($data['alertas']['proximos_vencer']->count() > 0)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg shadow border-l-4 border-yellow-500">
                        <h3 class="text-xl font-bold mb-4 flex items-center text-yellow-700 dark:text-yellow-300">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 mr-2" />
                            Próximos a Vencer ({{ $data['alertas']['dias_alerta'] }} días)
                        </h3>
                        <div class="space-y-2">
                            @foreach($data['alertas']['proximos_vencer'] as $fee)
                                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded">
                                    <div>
                                        <a href="{{ url("/admin/resources/fees/{$fee->id}/view") }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-600">{{ $fee->nombre }}</a>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Cliente: {{ $fee->contrato->cliente->name ?? 'Sin cliente' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-yellow-700 dark:text-yellow-300">
                                            {{ \Carbon\Carbon::parse($fee->fecha_fin)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($fee->fecha_fin)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Vencidos --}}
                @if($data['alertas']['vencidos']->count() > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg shadow border-l-4 border-red-500">
                        <h3 class="text-xl font-bold mb-4 flex items-center text-red-700 dark:text-red-300">
                            <x-heroicon-o-x-circle class="w-6 h-6 mr-2" />
                            Fees Vencidos ({{ $data['alertas']['vencidos']->count() }})
                        </h3>
                        <div class="space-y-2">
                            @foreach($data['alertas']['vencidos'] as $fee)
                                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded">
                                    <div>
                                        <a href="{{ url("/admin/resources/fees/{$fee->id}/view") }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-600">{{ $fee->nombre }}</a>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Cliente: {{ $fee->contrato->cliente->name ?? 'Sin cliente' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-red-700 dark:text-red-300">
                                            {{ \Carbon\Carbon::parse($fee->fecha_fin)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Venció {{ \Carbon\Carbon::parse($fee->fecha_fin)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-700">
                <p class="text-sm text-green-700 dark:text-green-300 flex items-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                    No hay fees próximos a vencer o vencidos.
                </p>
            </div>
        @endif

        {{-- Top Rankings --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Top Consumo --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-3">Top 5 Fees por Consumo</h3>
                @if(count($data['top_consumo']) > 0)
                    <ul class="space-y-2">
                        @foreach($data['top_consumo'] as $f)
                            <li class="flex justify-between text-sm border-b border-gray-200 dark:border-gray-700 pb-2">
                                <div>
                                    <a href="{{ url("/admin/resources/fees/{$f['id']}/view") }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-600">{{ $f['nombre'] }}</a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $f['cliente'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold {{ $f['porcentaje'] >= 90 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">{{ $f['porcentaje'] }}%</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($f['horas_consumidas'], 0) }}/{{ number_format($f['horas_incluidas'], 0) }}h</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay datos disponibles.</p>
                @endif
            </div>

            {{-- Top Revenue --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-3">Top 5 Fees por Revenue Mensual</h3>
                @if(count($data['top_revenue']) > 0)
                    <ul class="space-y-2">
                        @foreach($data['top_revenue'] as $f)
                            <li class="flex justify-between text-sm border-b border-gray-200 dark:border-gray-700 pb-2">
                                <div>
                                    <a href="{{ url("/admin/resources/fees/{$f['id']}/view") }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-600">{{ $f['nombre'] }}</a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $f['cliente'] }}</div>
                                </div>
                                <div class="font-semibold text-indigo-600 dark:text-indigo-400">${{ number_format($f['valor_mensual'], 0) }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay datos disponibles.</p>
                @endif
            </div>
        </div>

        {{-- Distribución por Cliente --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <x-heroicon-o-building-office class="w-6 h-6 mr-2" />
                Distribución por Cliente (Fees Activos)
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cliente</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fees</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Revenue Mensual</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas Incluidas</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas Consumidas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Consumo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($data['por_cliente'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/60">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $row['cliente'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ $row['fees'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-indigo-600 dark:text-indigo-400">${{ number_format($row['revenue_mensual'], 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['horas_incluidas'], 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['horas_consumidas'], 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $pct = $row['horas_incluidas'] > 0 ? round(($row['horas_consumidas'] / $row['horas_incluidas']) * 100, 1) : 0;
                                    @endphp
                                    <div class="w-40">
                                        <div class="flex justify-between text-xs mb-1 text-gray-600 dark:text-gray-400">
                                            <span>{{ $pct }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                            <div class="h-3 rounded-full {{ $pct >= 90 ? 'bg-red-600' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                                                 style="width: {{ min($pct, 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($data['por_cliente']))
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No se encontraron datos con los filtros seleccionados.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>