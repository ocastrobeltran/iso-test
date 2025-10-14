<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'email_verified_at',
        'remember_token',
        'roles',
        'carga_horaria_mensual',
        'clockify_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'dashboard') {
            return str_ends_with($this->email, '@leggercolombia.com');
        }

        if ($panel->getId() === 'clientes') {
            return true;
        }

        return false;
    }
    public static function canViewAny(): bool
	{
		return auth()->user()?->can('view_any_user');
	}

    public function contratosAsignados()
    {
        return $this->belongsToMany(Contrato::class, 'contrato_recurso', 'user_id', 'contrato_id')
            ->withPivot('horas_asignadas');
    }

    public function getTotalHorasAsignadasAttribute()
    {
        return $this->contratosAsignados()->sum('contrato_recurso.horas_asignadas');
    }

    public function getHorasClockify($start, $end)
    {
        $clockify = app(\App\Services\ClockifyService::class);
        return $clockify->getUserTimeEntries($this->clockify_id, $start, $end);
    }
}
