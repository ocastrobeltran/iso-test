<div class="space-y-3">
    @php
        $comentarios = is_array($getState()) ? $getState() : [];
    @endphp

    @if(empty($comentarios))
        <div class="text-sm text-gray-500 dark:text-gray-400 italic">
            No hay comentarios todavía
        </div>
    @else
        @foreach($comentarios as $comentario)
            @php
                $usuarioId = $comentario['usuario_id'] ?? 'Desconocido';
                $usuario = is_numeric($usuarioId) 
                    ? \App\Models\User::find($usuarioId)?->name ?? "Usuario #{$usuarioId}"
                    : $usuarioId;
                $contenido = $comentario['contenido'] ?? '';
                $fecha = $comentario['fecha'] ?? '';
                
                // Detectar tipo de comentario
                $esDevuelto = str_starts_with($contenido, '[DEVUELTO]');
                $esCierre = str_starts_with($contenido, '[CIERRE]');
            @endphp

            <div class="p-4 rounded-lg border @if($esDevuelto) border-orange-300 bg-orange-50 dark:border-orange-700 dark:bg-orange-950 @elseif($esCierre) border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-950 @else border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800 @endif">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-sm @if($esDevuelto) text-orange-900 dark:text-orange-100 @elseif($esCierre) text-green-900 dark:text-green-100 @else text-gray-900 dark:text-gray-100 @endif">
                            {{ $usuario }}
                        </span>
                        @if($esDevuelto)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                DEVUELTO
                            </span>
                        @elseif($esCierre)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-200">
                                CIERRE
                            </span>
                        @endif
                    </div>
                    <span class="text-xs @if($esDevuelto || $esCierre) text-opacity-75 @endif text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i') }}
                    </span>
                </div>
                <div class="text-sm @if($esDevuelto) text-orange-800 dark:text-orange-200 @elseif($esCierre) text-green-800 dark:text-green-200 @else text-gray-700 dark:text-gray-300 @endif whitespace-pre-wrap">
                    {{ preg_replace('/^\[(DEVUELTO|CIERRE)\]\s*/', '', $contenido) }}
                </div>
            </div>
        @endforeach
    @endif
</div>