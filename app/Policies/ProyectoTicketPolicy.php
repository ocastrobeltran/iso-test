<?php

namespace App\Policies;

use App\Models\ProyectoTicket;
use App\Models\User;

class ProyectoTicketPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_proyecto_ticket');
    }

    public function view(User $user, ProyectoTicket $proyectoTicket)
    {
        return $user->can('view_proyecto_ticket');
    }

    public function create(User $user)
    {
        return $user->can('create_proyecto_ticket');
    }

    public function update(User $user, ProyectoTicket $proyectoTicket)
    {
        return $user->can('update_proyecto_ticket');
    }

    public function delete(User $user, ProyectoTicket $proyectoTicket)
    {
        return $user->can('delete_proyecto_ticket');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_proyecto_ticket');
    }

    public function restore(User $user, ProyectoTicket $proyectoTicket)
    {
        return $user->can('restore_proyecto_ticket');
    }

    public function forceDelete(User $user, ProyectoTicket $proyectoTicket)
    {
        return $user->can('force_delete_proyecto_ticket');
    }
}