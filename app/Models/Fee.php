<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class Fee extends Model
{
    protected $table = 'fees';

    protected $casts = [
        'mes' => 'string',
        'is_demanda' => 'boolean',
        'horas_contratadas' => 'integer',
        'horas_ejecutadas' => 'integer',
        'valor_mensual' => 'decimal:2',
        'cliente_id' => 'integer',
        'pm_responsable_id' => 'integer',
        'proyecto_id' => 'integer',
    ];

    protected $fillable = [
        'nombre',
        'estado',
        'descripcion',
        'cliente_id',
        'contrato_id',
        'pm_responsable_id',
        'proyecto_id',
        'mes',
        'is_demanda',
        'horas_contratadas',
        'horas_ejecutadas',
        'valor_mensual',
        'notas',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'cliente_id');
    }

    public function pmResponsable(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'pm_responsable_id');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Proyecto::class, 'proyecto_id');
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Servicio::class, 'fee_servicio', 'fee_id', 'servicio_id');
    }

    public function recursos(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'fee_recurso', 'fee_id', 'user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'contrato_id', 'contrato_id');
    }

    public function contrato()
    {
        return $this->belongsTo(\App\Models\Contrato::class, 'contrato_id');
    }

    public function contratos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Contrato::class, 'fee_contrato', 'fee_id', 'contrato_id');
    }

    // Helpers
    public function getHorasRestantesAttribute()
    {
        if ($this->is_demanda) {
            return null;
        }

        return max(0, ($this->horas_contratadas ?? 0) - ($this->horas_ejecutadas ?? 0));
    }
}