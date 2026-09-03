<h2>New Support Ticket</h2>

<p>A new support ticket has been created.</p>

<p>
    <strong>Ticket ID:</strong>
    {{ $ticket->id }}
</p>

<p>
    <strong>Title:</strong>
    {{ $ticket->title }}
</p>

<p>
    <strong>Priority:</strong>
    {{ $ticket->priority }}
</p>

<p>
    <a href="{{ $editUrl }}">
        Edit Ticket
    </a>
</p>