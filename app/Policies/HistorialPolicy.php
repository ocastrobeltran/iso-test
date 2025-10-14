<?php

namespace App\Policies;

use App\Models\Historial;
use App\Models\User;

class HistorialPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_historial');
    }

    public function view(User $user, Historial $historial)
    {
        return $user->can('view_historial');
    }

    public function create(User $user)
    {
        return $user->can('create_historial');
    }

    public function update(User $user, Historial $historial)
    {
        return $user->can('update_historial');
    }

    public function delete(User $user, Historial $historial)
    {
        return $user->can('delete_historial');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_historial');
    }

    public function restore(User $user, Historial $historial)
    {
        return $user->can('restore_historial');
    }

    public function forceDelete(User $user, Historial $historial)
    {
        return $user->can('force_delete_historial');
    }
}