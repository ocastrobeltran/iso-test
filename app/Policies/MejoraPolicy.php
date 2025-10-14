<?php

namespace App\Policies;

use App\Models\Mejora;
use App\Models\User;

class MejoraPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_mejora');
    }

    public function view(User $user, Mejora $mejora)
    {
        return $user->can('view_mejora');
    }

    public function create(User $user)
    {
        return $user->can('create_mejora');
    }

    public function update(User $user, Mejora $mejora)
    {
        return $user->can('update_mejora');
    }

    public function delete(User $user, Mejora $mejora)
    {
        return $user->can('delete_mejora');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_mejora');
    }

    public function restore(User $user, Mejora $mejora)
    {
        return $user->can('restore_mejora');
    }

    public function forceDelete(User $user, Mejora $mejora)
    {
        return $user->can('force_delete_mejora');
    }
}