<?php

namespace App\Policies;

use App\Models\Contrato;
use App\Models\User;

class ContratoPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_contrato');
    }

    public function view(User $user, Contrato $contrato)
    {
        return $user->can('view_contrato');
    }

    public function create(User $user)
    {
        return $user->can('create_contrato');
    }

    public function update(User $user, Contrato $contrato)
    {
        return $user->can('update_contrato');
    }

    public function delete(User $user, Contrato $contrato)
    {
        return $user->can('delete_contrato');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_contrato');
    }

    public function restore(User $user, Contrato $contrato)
    {
        return $user->can('restore_contrato');
    }

    public function forceDelete(User $user, Contrato $contrato)
    {
        return $user->can('force_delete_contrato');
    }
}