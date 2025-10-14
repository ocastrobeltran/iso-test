<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_any_ticket');
    }

    public function view(User $user, Ticket $ticket)
    {
        return $user->can('view_ticket');
    }

    public function create(User $user)
    {
        return $user->can('create_ticket');
    }

    public function update(User $user, Ticket $ticket)
    {
        return $user->can('update_ticket');
    }

    public function delete(User $user, Ticket $ticket)
    {
        return $user->can('delete_ticket');
    }
}