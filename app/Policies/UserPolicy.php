<?php

namespace App\Policies;

use App\Models\User;
use App\Models\User as UserModel;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_user');
    }

    public function view(User $user, UserModel $userModel)
    {
        return $user->can('view_user');
    }

    public function create(User $user)
    {
        return $user->can('create_user');
    }

    public function update(User $user, UserModel $userModel)
    {
        return $user->can('update_user');
    }

    public function delete(User $user, UserModel $userModel)
    {
        return $user->can('delete_user');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_user');
    }

    public function restore(User $user, UserModel $userModel)
    {
        return $user->can('restore_user');
    }

    public function forceDelete(User $user, UserModel $userModel)
    {
        return $user->can('force_delete_user');
    }
}