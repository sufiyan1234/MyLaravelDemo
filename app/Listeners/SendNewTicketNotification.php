<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Mail\QueueNewTicketCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewTicketNotification implements ShouldQueue
{
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        $ticket->load([
            'creator:id,name,email',
            'agent:id,name,email',
        ]);

        $admins = User::where('role', 'admin')->get();

        $editUrl = config('app.frontend_url')
            . '/admin/tickets/'
            . $ticket->id
            . '/edit';

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->send(new QueueNewTicketCreated($ticket, $editUrl));
        }
    }
}