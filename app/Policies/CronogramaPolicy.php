<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cronograma;

class CronogramaPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_cronograma');
    }

    public function view(User $user, Cronograma $cronograma)
    {
        return $user->can('view_cronograma');
    }

    public function create(User $user)
    {
        return $user->can('create_cronograma');
    }

    public function update(User $user, Cronograma $cronograma)
    {
        return $user->can('update_cronograma');
    }

    public function delete(User $user, Cronograma $cronograma)
    {
        return $user->can('delete_cronograma');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_cronograma');
    }

    public function restore(User $user, Cronograma $cronograma)
    {
        return $user->can('restore_cronograma');
    }

    public function forceDelete(User $user, Cronograma $cronograma)
    {
        return $user->can('force_delete_cronograma');
    }
}