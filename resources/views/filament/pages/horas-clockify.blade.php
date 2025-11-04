{{-- filepath: resources/views/filament/pages/horas-clockify.blade.php --}}

@foreach ($horas as $hora)
    <div>
        <p>Proyecto: {{ $hora['project'] }}</p>
        <p>Cliente: {{ $hora['client'] }}</p>
        <p>Descripción: {{ $hora['description'] }}</p>
        <p>Horas reportadas: {{ $hora['duration'] }}</p>
        @if ($hora['alerta'] === 'Excedido')
            <span class="alert alert-danger">¡Alerta! Horas excedidas.</span>
        @else
            <span class="alert alert-success">Horas dentro de límites.</span>
        @endif
    </div>
@endforeach

<x-filament::page>
    <form wire:submit.prevent="consultarHoras" class="space-y-4">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Usuario</label>
                <select wire:model="userId" class="filament-forms-select block w-full bg-white dark:bg-gray-900 text-black dark:text-white border-gray-300 dark:border-gray-700">
                    <option value="">Seleccione...</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Desde</label>
                <input type="date" wire:model="fechaInicio" class="filament-forms-input block w-full bg-white dark:bg-gray-900 text-black dark:text-white border-gray-300 dark:border-gray-700" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Hasta</label>
                <input type="date" wire:model="fechaFin" class="filament-forms-input block w-full bg-white dark:bg-gray-900 text-black dark:text-white border-gray-300 dark:border-gray-700" />
            </div>
            <div class="flex items-end">
                <x-filament::button type="submit">
                    Consultar
                </x-filament::button>
            </div>
        </div>
    </form>

    @if($horas && count($horas))
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700 border border-gray-400 dark:border-gray-600 rounded-lg">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold border-b border-gray-300 dark:border-gray-700">Proyecto</th>
                        <th class="px-4 py-2 text-left font-semibold border-b border-gray-300 dark:border-gray-700">Cliente</th>
                        <th class="px-4 py-2 text-left font-semibold border-b border-gray-300 dark:border-gray-700">Descripción</th>
                        <th class="px-4 py-2 text-left font-semibold border-b border-gray-300 dark:border-gray-700">Inicio</th>
                        <th class="px-4 py-2 text-left font-semibold border-b border-gray-300 dark:border-gray-700">Fin</th>
                        <th class="px-4 py-2 text-left font-semibold border-b border-gray-300 dark:border-gray-700">Duración (h)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($horas as $i => $h)
                        <tr class="{{ $i % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }} hover:bg-blue-50 dark:hover:bg-blue-900 transition">
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-800">{{ $h['project'] }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-800">{{ $h['client'] }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-800">{{ $h['description'] }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-800">{{ $h['start'] }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-800">{{ $h['end'] }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-800">{{ $h['duration'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($userId)
        <div class="mt-6 text-gray-500">No hay registros para el usuario y rango seleccionado.</div>
    @endif
</x-filament::page>