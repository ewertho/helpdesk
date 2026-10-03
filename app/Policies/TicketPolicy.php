<?php

namespace App\Policies;

use App\Enums\Status;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isStaff() || $ticket->requester_id === $user->id;
    }

    /** O cliente pode dar o próprio chamado por resolvido ou encerrá-lo, nada além disso. */
    public function changeStatus(User $user, Ticket $ticket, Status $to): bool
    {
        return $user->isStaff() || ($ticket->requester_id === $user->id && $to->isDone());
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket) && $ticket->status !== Status::Closed;
    }
}
