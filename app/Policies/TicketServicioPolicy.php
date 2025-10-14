<?php

namespace App\Policies;

use App\Models\TicketServicio;
use App\Models\User;

class TicketServicioPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_ticket_servicio');
    }

    public function view(User $user, TicketServicio $ticketServicio)
    {
        return $user->can('view_ticket_servicio');
    }

    public function create(User $user)
    {
        return $user->can('create_ticket_servicio');
    }

    public function update(User $user, TicketServicio $ticketServicio)
    {
        return $user->can('update_ticket_servicio');
    }

    public function delete(User $user, TicketServicio $ticketServicio)
    {
        return $user->can('delete_ticket_servicio');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_ticket_servicio');
    }

    public function restore(User $user, TicketServicio $ticketServicio)
    {
        return $user->can('restore_ticket_servicio');
    }

    public function forceDelete(User $user, TicketServicio $ticketServicio)
    {
        return $user->can('force_delete_ticket_servicio');
    }
}