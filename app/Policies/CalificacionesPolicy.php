<?php

namespace App\Policies;

use App\Models\Calificacion;
use App\Models\User;

class CalificacionesPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_calificacion');
    }

    public function view(User $user, Calificacion $calificacion)
    {
        return $user->can('view_calificacion');
    }

    public function create(User $user)
    {
        return $user->can('create_calificacion');
    }

    public function update(User $user, Calificacion $calificacion)
    {
        return $user->can('update_calificacion');
    }

    public function delete(User $user, Calificacion $calificacion)
    {
        return $user->can('delete_calificacion');
    }
}