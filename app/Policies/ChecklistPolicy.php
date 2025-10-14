<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Checklist;

class ChecklistPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_checklist');
    }

    public function view(User $user, Checklist $checklist)
    {
        return $user->can('view_checklist');
    }

    public function create(User $user)
    {
        return $user->can('create_checklist');
    }

    public function update(User $user, Checklist $checklist)
    {
        return $user->can('update_checklist');
    }

    public function delete(User $user, Checklist $checklist)
    {
        return $user->can('delete_checklist');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_checklist');
    }

    public function restore(User $user, Checklist $checklist)
    {
        return $user->can('restore_checklist');
    }

    public function forceDelete(User $user, Checklist $checklist)
    {
        return $user->can('force_delete_checklist');
    }
}