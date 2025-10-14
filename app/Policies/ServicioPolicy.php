<?php

namespace App\Policies;

use App\Models\Servicio;
use App\Models\User;

class ServicioPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_servicio');
    }

    public function view(User $user, Servicio $servicio)
    {
        return $user->can('view_servicio');
    }

    public function create(User $user)
    {
        return $user->can('create_servicio');
    }

    public function update(User $user, Servicio $servicio)
    {
        return $user->can('update_servicio');
    }

    public function delete(User $user, Servicio $servicio)
    {
        return $user->can('delete_servicio');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_servicio');
    }

    public function restore(User $user, Servicio $servicio)
    {
        return $user->can('restore_servicio');
    }

    public function forceDelete(User $user, Servicio $servicio)
    {
        return $user->can('force_delete_servicio');
    }
}