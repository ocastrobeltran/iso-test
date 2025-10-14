<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\User;

class UsuarioPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_usuario');
    }

    public function view(User $user, Usuario $usuario)
    {
        return $user->can('view_usuario');
    }

    public function create(User $user)
    {
        return $user->can('create_usuario');
    }

    public function update(User $user, Usuario $usuario)
    {
        return $user->can('update_usuario');
    }

    public function delete(User $user, Usuario $usuario)
    {
        return $user->can('delete_usuario');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_usuario');
    }

    public function restore(User $user, Usuario $usuario)
    {
        return $user->can('restore_usuario');
    }

    public function forceDelete(User $user, Usuario $usuario)
    {
        return $user->can('force_delete_usuario');
    }
}