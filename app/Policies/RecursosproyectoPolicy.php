<?php

namespace App\Policies;

use App\Models\Recursosproyecto;
use App\Models\User;

class RecursosproyectoPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_recursosproyecto');
    }

    public function view(User $user, Recursosproyecto $recursosproyecto)
    {
        return $user->can('view_recursosproyecto');
    }

    public function create(User $user)
    {
        return $user->can('create_recursosproyecto');
    }

    public function update(User $user, Recursosproyecto $recursosproyecto)
    {
        return $user->can('update_recursosproyecto');
    }

    public function delete(User $user, Recursosproyecto $recursosproyecto)
    {
        return $user->can('delete_recursosproyecto');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_recursosproyecto');
    }

    public function restore(User $user, Recursosproyecto $recursosproyecto)
    {
        return $user->can('restore_recursosproyecto');
    }

    public function forceDelete(User $user, Recursosproyecto $recursosproyecto)
    {
        return $user->can('force_delete_recursosproyecto');
    }
}