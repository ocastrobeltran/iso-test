<?php

namespace App\Policies;

use App\Models\ProyectoServicio;
use App\Models\User;

class ProyectoServicioPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_proyecto_servicio');
    }

    public function view(User $user, ProyectoServicio $proyectoServicio)
    {
        return $user->can('view_proyecto_servicio');
    }

    public function create(User $user)
    {
        return $user->can('create_proyecto_servicio');
    }

    public function update(User $user, ProyectoServicio $proyectoServicio)
    {
        return $user->can('update_proyecto_servicio');
    }

    public function delete(User $user, ProyectoServicio $proyectoServicio)
    {
        return $user->can('delete_proyecto_servicio');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_proyecto_servicio');
    }

    public function restore(User $user, ProyectoServicio $proyectoServicio)
    {
        return $user->can('restore_proyecto_servicio');
    }

    public function forceDelete(User $user, ProyectoServicio $proyectoServicio)
    {
        return $user->can('force_delete_proyecto_servicio');
    }
}