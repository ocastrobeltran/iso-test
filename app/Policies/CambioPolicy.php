<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cambio;

class CambioPolicy

{
    public function viewAny(User $user)
    {
        return $user->can('view_any_cambio');
    }

    public function view(User $user, Cambio $cambio)
    {
        return $user->can('view_cambio');
    }

    public function create(User $user)
    {
        return $user->can('create_cambio');
    }

    public function update(User $user, Cambio $cambio)
    {
        return $user->can('update_cambio');
    }

    public function delete(User $user, Cambio $cambio)
    {
        return $user->can('delete_cambio');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_cambio');
    }

    public function restore(User $user, Cambio $cambio)
    {
        return $user->can('restore_cambio');
    }

    public function forceDelete(User $user, Cambio $cambio)
    {
        return $user->can('force_delete_cambio');
    }
}