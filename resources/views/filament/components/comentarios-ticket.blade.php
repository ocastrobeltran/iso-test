@if(empty($comentarios))
    <div>Sin comentarios</div>
@else
    @foreach(collect($comentarios)->sortBy('fecha') as $comentario)
        @php
            if (!isset($comentario['usuario_id'])) continue;
            $usuario = \App\Models\User::find($comentario['usuario_id']);
            $nombre = $usuario ? $usuario->name : 'Usuario eliminado';
            $fecha = isset($comentario['fecha']) ? \Carbon\Carbon::parse($comentario['fecha'])->format('d/m/Y H:i') : '';
            $contenido = nl2br(e($comentario['contenido'] ?? ''));
        @endphp
        <div style="margin-bottom:10px;">
            <b>{{ $nombre }}</b>
            <span style="color:gray;font-size:12px;">({{ $fecha }})</span><br>
            {!! $contenido !!}
        </div>
    @endforeach
@endif