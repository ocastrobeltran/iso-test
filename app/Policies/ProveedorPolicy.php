<?php

namespace App\Policies;

use App\Models\Proveedor;
use App\Models\User;

class ProveedorPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_proveedor');
    }

    public function view(User $user, Proveedor $proveedor)
    {
        return $user->can('view_proveedor');
    }

    public function create(User $user)
    {
        return $user->can('create_proveedor');
    }

    public function update(User $user, Proveedor $proveedor)
    {
        return $user->can('update_proveedor');
    }

    public function delete(User $user, Proveedor $proveedor)
    {
        return $user->can('delete_proveedor');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_proveedor');
    }

    public function restore(User $user, Proveedor $proveedor)
    {
        return $user->can('restore_proveedor');
    }

    public function forceDelete(User $user, Proveedor $proveedor)
    {
        return $user->can('force_delete_proveedor');
    }
}