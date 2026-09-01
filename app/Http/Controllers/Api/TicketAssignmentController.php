<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketAssignmentController extends Controller
{
    public function assign(
        Request $request,
        Ticket $ticket
    ) {
        $this->authorize('assign', $ticket);

        $validated = $request->validate([
            'agent_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $agent = User::findOrFail(
            $validated['agent_id']
        );

        if (!$agent->isAgent()) {

            return response()->json([
                'message' => 'Selected user is not an agent.',
            ], 422);
        }

        $ticket->update([
            'assigned_to' => $agent->id,
        ]);

        $ticket->load([
            'creator:id,name,email',
            'agent:id,name,email',
        ]);

        return response()->json([
            'message' => 'Ticket assigned successfully',

            'ticket' => $ticket,
        ]);
    }

    public function agents()
    {
        $agents = User::query()
            ->where('role', 'agent')
            ->select([
                'id',
                'name',
                'email',
            ])
            ->orderBy('name')
            ->get();

        return response()->json([
            'agents' => $agents,
        ]);
    }
}