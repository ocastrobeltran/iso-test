<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Detalleconsumo;

class DetalleconsumoPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_detalleconsumo');
    }

    public function view(User $user, Detalleconsumo $detalleconsumo)
    {
        return $user->can('view_detalleconsumo');
    }

    public function create(User $user)
    {
        return $user->can('create_detalleconsumo');
    }

    public function update(User $user, Detalleconsumo $detalleconsumo)
    {
        return $user->can('update_detalleconsumo');
    }

    public function delete(User $user, Detalleconsumo $detalleconsumo)
    {
        return $user->can('delete_detalleconsumo');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_detalleconsumo');
    }

    public function restore(User $user, Detalleconsumo $detalleconsumo)
    {
        return $user->can('restore_detalleconsumo');
    }

    public function forceDelete(User $user, Detalleconsumo $detalleconsumo)
    {
        return $user->can('force_delete_detalleconsumo');
    }
}