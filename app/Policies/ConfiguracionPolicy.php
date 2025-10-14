<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Configuracion;

class ConfiguracionPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_configuracion');
    }

    public function view(User $user, Configuracion $configuracion)
    {
        return $user->can('view_configuracion');
    }

    public function create(User $user)
    {
        return $user->can('create_configuracion');
    }

    public function update(User $user, Configuracion $configuracion)
    {
        return $user->can('update_configuracion');
    }

    public function delete(User $user, Configuracion $configuracion)
    {
        return $user->can('delete_configuracion');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_configuracion');
    }

    public function restore(User $user, Configuracion $configuracion)
    {
        return $user->can('restore_configuracion');
    }

    public function forceDelete(User $user, Configuracion $configuracion)
    {
        return $user->can('force_delete_configuracion');
    }
}