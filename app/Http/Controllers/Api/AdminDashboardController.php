<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $total = Ticket::count();

        $open = Ticket::where(
            'status',
            'open'
        )->count();

        $inProgress = Ticket::where(
            'status',
            'in_progress'
        )->count();

        $closed = Ticket::where(
            'status',
            'closed'
        )->count();

        $urgent = Ticket::where(
            'priority',
            'urgent'
        )->count();

        $high = Ticket::where(
            'priority',
            'high'
        )->count();

        return response()->json([
            'tickets' => [
                'total' => $total,

                'open' => $open,

                'in_progress' => $inProgress,

                'closed' => $closed,
            ],

            'priority' => [
                'urgent' => $urgent,

                'high' => $high,

                'medium' => Ticket::where(
                    'priority',
                    'medium'
                )->count(),

                'low' => Ticket::where(
                    'priority',
                    'low'
                )->count(),
            ],
        ]);
    }
}