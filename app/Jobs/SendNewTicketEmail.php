<?php

namespace App\Jobs;

use App\Mail\QueueNewTicketCreated;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewTicketEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $adminEmail,
        public string $editUrl
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->adminEmail)
            ->send(new QueueNewTicketCreated(
                $this->ticket,
                $this->editUrl
            ));
    }
}