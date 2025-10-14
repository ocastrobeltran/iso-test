{{-- resources/views/filament/resources/contrato-resource/widgets/recursos-asignados.blade.php --}}
<x-filament::section>
    <x-slot name="header">Recursos asignados</x-slot>
    <span><strong>Recursos asignados:</strong></span>
    <br><br>
    <ul>
        @forelse($recursos as $recurso)
            <li>{{ $recurso->name }} ({{ $recurso->pivot->horas_asignadas }} h)</li>
        @empty
            <li>No hay recursos asignados.</li>
        @endforelse
    </ul>
</x-filament::section>