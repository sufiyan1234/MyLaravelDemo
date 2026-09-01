<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        // Admin can see everything
        if ($user->isAdmin()) {
            return true;
        }

        // Ticket creator can see their ticket
        if ($ticket->user_id === $user->id) {
            return true;
        }

        // Assigned agent can see ticket
        if ($ticket->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        // Admin can update everything
        if ($user->isAdmin()) {
            return true;
        }

        // Agent can update tickets assigned to them
        if (
            $user->isAgent() &&
            $ticket->assigned_to === $user->id
        ) {
            return true;
        }

        return false;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}