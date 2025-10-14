<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Sección de Filtros -->
        <x-filament::section>
            <x-slot name="heading">
                Filtros de Reporte
            </x-slot>
            
            <form wire:submit.prevent="aplicarFiltros" class="space-y-4">
                <div class="grid grid-cols-4 grid-flow-col md:grid-cols-4 gap-4">
                    <!-- Filtro por Proyecto -->
                    <div>
                        <label for="filter_proyecto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Proyecto
                        </label>
                        <select 
                            wire:model.defer="filter_proyecto_id" 
                            id="filter_proyecto_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todos los proyectos</option>
                            @foreach($this->proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por Estado -->
                    <div>
                        <label for="filter_estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Estado
                        </label>
                        <select 
                            wire:model.defer="filter_estado" 
                            id="filter_estado"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todos los estados</option>
                            @foreach($this->estados as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro Fecha Inicio -->
                    <div>
                        <label for="filter_fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha Inicio
                        </label>
                        <input
                            type="date"
                            wire:model.defer="filter_fecha_inicio"
                            id="filter_fecha_inicio"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Filtro Fecha Fin -->
                    <div>
                        <label for="filter_fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha Fin
                        </label>
                        <input
                            type="date"
                            wire:model.defer="filter_fecha_fin"
                            id="filter_fecha_fin"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-3">
                    <x-filament::button type="submit" color="primary">
                        <x-heroicon-m-funnel class="w-4 h-4 mr-2" />
                        Aplicar Filtros
                    </x-filament::button>
                    
                    <x-filament::button 
                        type="button" 
                        color="gray" 
                        wire:click="limpiarFiltros"
                    >
                        <x-heroicon-m-x-mark class="w-4 h-4 mr-2" />
                        Limpiar
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <!-- KPIs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Tickets -->
            <x-filament::section class="text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        {{ number_format($this->kpis['total_tickets']) }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Total Tickets
                    </div>
                </div>
            </x-filament::section>

            <!-- Tickets Abiertos -->
            <x-filament::section class="text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-danger-600 dark:text-danger-400">
                        {{ number_format($this->kpis['tickets_abiertos']) }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Abiertos
                    </div>
                </div>
            </x-filament::section>

            <!-- Tickets En Progreso -->
            <x-filament::section class="text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-warning-600 dark:text-warning-400">
                        {{ number_format($this->kpis['tickets_en_progreso']) }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        En Progreso
                    </div>
                </div>
            </x-filament::section>

            <!-- Tickets Cerrados -->
            <x-filament::section class="text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-success-600 dark:text-success-400">
                        {{ number_format($this->kpis['tickets_cerrados']) }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Cerrados
                    </div>
                </div>
            </x-filament::section>

            <!-- Tickets Resueltos -->
            <x-filament::section class="text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-info-600 dark:text-info-400">
                        {{ number_format($this->kpis['tickets_resueltos']) }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Resueltos
                    </div>
                </div>
            </x-filament::section>

            <!-- SLA Promedio -->
            <x-filament::section class="text-center">
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $this->kpis['sla_promedio'] }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        SLA Promedio (días)
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Tickets por Proyecto -->
        @if($this->kpis['tickets_por_proyecto']->count() > 0)
        <x-filament::section>
            <x-slot name="heading">
                Tickets por Proyecto
            </x-slot>
            
            <div class="space-y-3">
                @foreach($this->kpis['tickets_por_proyecto'] as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item->nombre }}</span>
                    <span class="px-2 py-1 text-sm font-semibold bg-primary-100 text-primary-800 dark:bg-primary-800 dark:text-primary-100 rounded-full">
                        {{ $item->total }}
                    </span>
                </div>
                @endforeach
            </div>
        </x-filament::section>
        @endif

        <!-- Botón Exportar a CSV -->
        <div class="flex justify-end mb-2">
            <form method="POST" action="{{ route('exportar.tickets.csv') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition"
                >
                    <x-heroicon-m-arrow-down-tray class="w-4 h-4 mr-2" />
                    Exportar Tickets a CSV
                </button>
            </form>
        </div>

        <!-- Tabla de Tickets Recientes -->
        <x-filament::section>
            <x-slot name="heading">
                Tickets Recientes (Últimos 50)
            </x-slot>
            
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Título
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Proyecto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Fecha Creación
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->ticketsDetalle as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $ticket->titulo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $ticket->proyecto_nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeClasses = match($ticket->estado) {
                                        'Abierto' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'En Progreso' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'Resuelto' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'Cerrado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClasses }}">
                                    {{ $ticket->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay tickets que mostrar con los filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>