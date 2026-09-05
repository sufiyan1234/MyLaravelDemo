<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewTicketCreated;
use App\Mail\QueueNewTicketCreated;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with([
            'creator:id,name,email',
            'agent:id,name,email',
        ]);

        /*
        |--------------------------------------------------------------------------
        | User sees tickets they created
        |--------------------------------------------------------------------------
        */

        if ($user->isRegularUser()) {

            $query->where(
                'user_id',
                $user->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Agent sees tickets assigned to them
        |--------------------------------------------------------------------------
        */

        elseif ($user->isAgent()) {

            $query->where(
                'assigned_to',
                $user->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Admin sees everything
        |--------------------------------------------------------------------------
        */

        // Admin requires no additional condition.

        // Filters
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        $tickets = $query
            ->latest()
            ->paginate(15);

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'required',
                'string'
            ],

            'priority' => [
                'sometimes',
                'in:low,medium,high,urgent'
            ],
        ]);

        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
            'user_id' => $request->user()->id,
            'assigned_to' => null,
        ]);

        $ticket->load([
            'creator:id,name,email',
            'agent:id,name,email',
        ]);

        // Find all administrators
        $admins = User::where('role', 'admin')->get();

        // Create the frontend edit URL
        $editUrl = config('app.frontend_url')
            . '/admin/tickets/'
            . $ticket->id
            . '/edit';

        // Send the new-ticket email to every administrator synchronously
        // foreach ($admins as $admin) {
        //     Mail::to($admin->email)
        //         ->send(new NewTicketCreated($ticket, $editUrl));
        // }

        // Send the new-ticket email to every administrator asynchronously via queue
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)
                    ->queue(new QueueNewTicketCreated($ticket, $editUrl));
            } catch (\Throwable $exception) {
                Log::error('Unable to queue new ticket email.', [
                    'ticket_id' => $ticket->id,
                    'admin_email' => $admin->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }


        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket,
        ], 201);
    }

    public function show(
        Request $request,
        Ticket $ticket
    ) {
        $this->authorize('view', $ticket);

        $ticket->load([
            'creator:id,name,email',
            'agent:id,name,email',
        ]);

        return response()->json([
            'ticket' => $ticket,
        ]);
    }

    public function update(
        Request $request,
        Ticket $ticket
    ) {
        $this->authorize('update', $ticket);

        $validated = $request->validate([

            'title' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'description' => [
                'sometimes',
                'string'
            ],

            'status' => [
                'sometimes',
                'in:open,in_progress,closed'
            ],

            'priority' => [
                'sometimes',
                'in:low,medium,high,urgent'
            ],
        ]);

        $ticket->update($validated);

        $ticket->load([
            'creator:id,name,email',
            'agent:id,name,email',
        ]);

        return response()->json([
            'message' => 'Ticket updated successfully',

            'ticket' => $ticket,
        ]);
    }
}