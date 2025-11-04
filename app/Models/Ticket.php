<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Ticket
 * 
 * @property int $id
 * @property string|null $titulo
 * @property string|null $descripcion
 * @property string|null $estado
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_asignacion
 * @property Carbon|null $fecha_cierre
 * @property Carbon|null $fecha_resolucion
 * @property string|null $solucion
 * @property string|null $comentarios
 * 
 * @property Collection|Calificacion[] $calificacions
 * @property Collection|Diagnostico[] $diagnosticos
 * @property Collection|Proyecto[] $proyectos
 * @property Collection|Detalleconsumo[] $detalleconsumos
 * @property Collection|Servicio[] $servicios
 * @property Collection|Usuario[] $usuarios
 *
 * @package App\Models
 */
class Ticket extends Model
{
	use HasRoles;
	use HasFactory;

	protected $table = 'ticket';
    public $timestamps = false;

	protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'contrato_id',
        'fecha_creacion',
        'fecha_asignacion',
        'fecha_cierre',
        'fecha_resolucion',
        'solucion',
        'comentarios',
        'tiempo_resolucion_estimada',
        // 'empleado_asignado_id',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_cierre' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'comentarios' => 'array',
    ];

	public function calificacions()
	{
		return $this->hasMany(Calificacion::class);
	}

	public function diagnosticos()
	{
		return $this->hasMany(Diagnostico::class);
	}

	public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_ticket', 'ticket_id', 'proyecto_id');
    }

	public function detalleconsumos()
	{
		return $this->belongsToMany(Detalleconsumo::class, 'ticket_detalleconsumo', 'ticket_id', 'detalle_id');
	}

	public function servicios()
	{
		return $this->belongsToMany(Servicio::class, 'ticket_servicio');
	}

    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\Usuario::class, 'ticket_usuario', 'ticket_id', 'usuario_id')->withPivot('rol');
    }

	// Relación con usuarios (clientes, empleados, etc.)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_users', 'ticket_id', 'user_id')
            ->withPivot('rol')
            ->withTimestamps();
    }

    // Cliente que creó el ticket
    public function cliente()
    {
        return $this->users()->wherePivot('rol', 'cliente')->first();
    }

    // Empleado asignado al ticket
    // public function empleadoAsignado(): BelongsTo
    // {
    //     return $this->belongsTo(Usuario::class, 'empleado_asignado_id');
    // }
    public function getEmpleadoAsignadoNombreAttribute()
    {
        return $this->usuarios()->wherePivot('rol', 'empleado')->first()?->nombre;
    }

    // Proyecto principal (el primero asociado)
    public function proyecto()
    {
        return $this->proyectos()->first();
    }

    // Scopes para filtrar por usuario
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeForCliente($query, $userId)
    {
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('ticket_users.user_id', $userId)->where('ticket_users.rol', 'cliente');
        });
    }

    public function getProyectoNombreAttribute()
    {
        return $this->proyectos()->first()?->nombre;
    }

    public function historiales()
    {
        return $this->morphMany(\App\Models\Historial::class, 'historialable');
    }

    public function getTiempoResolucionHorasAttribute()
    {
        if ($this->fecha_creacion && $this->fecha_resolucion) {
            return $this->fecha_creacion->diffInHours($this->fecha_resolucion);
        }
        return null;
    }

    /**
     * Tiempo de resolución en formato legible (ej: "2 días 3 horas")
     */
    public function getTiempoResolucionHumanoAttribute()
    {
        if ($this->fecha_creacion && $this->fecha_resolucion) {
            $totalHoras = $this->fecha_creacion->diffInHours($this->fecha_resolucion);
            $dias = intdiv($totalHoras, 8);
            $horas = $totalHoras % 8;
            $texto = [];
            if ($dias > 0) {
                $texto[] = $dias . ' día' . ($dias > 1 ? 's' : '');
            }
            if ($horas > 0 || $dias === 0) {
                $texto[] = $horas . ' hora' . ($horas !== 1 ? 's' : '');
            }
            return implode(' ', $texto);
        }
        return null;
    }

    public function getCumplimientoResolucionAttribute()
    {
        if ($this->tiempo_resolucion_estimada && $this->tiempo_resolucion_horas) {
            $porcentaje = ($this->tiempo_resolucion_horas / $this->tiempo_resolucion_estimada) * 100;
            return round($porcentaje, 1) . '%';
        }
        return null;
    }

    // public function getComentariosAttribute($value)
    // {
    //     if (empty($value)) {
    //         return 'Sin comentarios';
    //     }
    //     // Si es JSON, decodifica
    //     if (is_string($value)) {
    //         $comentarios = json_decode($value, true);
    //     } else {
    //         $comentarios = $value;
    //     }
    //     // Si no es array, fuerza a string
    //     if (!is_array($comentarios)) {
    //         return (string) $value;
    //     }
    //     // Si es array vacío
    //     if (empty($comentarios)) {
    //         return 'Sin comentarios';
    //     }
    //     // Formatea cada comentario como HTML, obteniendo el nombre del usuario
    //     return collect($comentarios)->map(function ($comentario) {
    //         if (!is_array($comentario)) {
    //             return '- ' . (string)$comentario;
    //         }
    //         $fecha = $comentario['fecha'] ?? '';
    //         $usuario_id = $comentario['usuario_id'] ?? ($comentario['user_id'] ?? null);
    //         $usuario_nombre = $usuario_id ? \App\Models\User::find($usuario_id)?->name ?? 'Desconocido' : 'Desconocido';
    //         $contenido = $comentario['contenido'] ?? '';
    //         return "<b>{$fecha}</b> [{$usuario_nombre}]: {$contenido} <br>";
    //     })->implode('<br>');
    // }

    public function getComentariosFormateadosAttribute()
    {
        $comentarios = $this->attributes['comentarios'] ?? null;
        
        if (empty($comentarios)) {
            return 'Sin comentarios';
        }
        
        // Si es JSON string, decodifica
        if (is_string($comentarios)) {
            $comentarios = json_decode($comentarios, true);
        }
        
        if (!is_array($comentarios) || empty($comentarios)) {
            return 'Sin comentarios';
        }
        
        // Formatea cada comentario como HTML
        return collect($comentarios)->map(function ($comentario) {
            if (!is_array($comentario)) {
                return '- ' . (string)$comentario;
            }
            $fecha = $comentario['fecha'] ?? '';
            $usuario_id = $comentario['usuario_id'] ?? ($comentario['user_id'] ?? null);
            $usuario_nombre = $usuario_id ? \App\Models\User::find($usuario_id)?->name ?? 'Desconocido' : 'Desconocido';
            $contenido = $comentario['contenido'] ?? '';
            return "<b>{$fecha}</b> [{$usuario_nombre}]: {$contenido}";
        })->implode('<br>');
    }

    // nuevo: relación directa por FK (no rompe la relación many-to-many existente)
    public function proyectoFk(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Proyecto::class, 'proyecto_id');
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Fee::class, 'fee_id');
    }

    /**
     * Devuelve el tipo asociado: 'proyecto' | 'fee' | null
     */
    public function getAsociadoTipoAttribute()
    {
        if ($this->proyecto_id) {
            return 'proyecto';
        }
        if ($this->fee_id) {
            return 'fee';
        }
        return null;
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Contrato::class, 'contrato_id');
    }

    /**
     * Nombre del asociado (prioriza FK directo; si no existe, intenta usar la relación many-to-many antigua)
     */
    public function getAsociadoNombreAttribute()
    {
        return $this->contrato?->titulo
            ?? $this->proyectos()->first()?->nombre
            ?? 'Sin asociado';
    }

    /**
     * URI o tipo-link para view (opcional)
     */
    public function getAsociadoLinkAttribute()
    {
        return $this->contrato ? url("/admin/resources/contrato/{$this->contrato->id}/view") : null;
    }
}
