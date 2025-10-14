<?php

namespace App\Policies;

use App\Models\Proyecto;
use App\Models\User;

class ProyectoPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_proyecto');
    }

    public function view(User $user, Proyecto $proyecto)
    {
        return $user->can('view_proyecto');
    }

    public function create(User $user)
    {
        return $user->can('create_proyecto');
    }

    public function update(User $user, Proyecto $proyecto)
    {
        return $user->can('update_proyecto');
    }

    public function delete(User $user, Proyecto $proyecto)
    {
        return $user->can('delete_proyecto');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_proyecto');
    }

    public function restore(User $user, Proyecto $proyecto)
    {
        return $user->can('restore_proyecto');
    }

    public function forceDelete(User $user, Proyecto $proyecto)
    {
        return $user->can('force_delete_proyecto');
    }
}