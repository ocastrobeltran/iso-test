<?php

namespace App\Policies;

use App\Models\Diagnostico;
use App\Models\User;

class DiagnosticoPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_diagnostico');
    }

    public function view(User $user, Diagnostico $diagnostico)
    {
        return $user->can('view_diagnostico');
    }

    public function create(User $user)
    {
        return $user->can('create_diagnostico');
    }

    public function update(User $user, Diagnostico $diagnostico)
    {
        return $user->can('update_diagnostico');
    }

    public function delete(User $user, Diagnostico $diagnostico)
    {
        return $user->can('delete_diagnostico');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_diagnostico');
    }

    public function restore(User $user, Diagnostico $diagnostico)
    {
        return $user->can('restore_diagnostico');
    }

    public function forceDelete(User $user, Diagnostico $diagnostico)
    {
        return $user->can('force_delete_diagnostico');
    }
}