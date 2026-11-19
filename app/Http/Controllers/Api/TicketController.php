<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Returns how many open tickets are assigned (responsible_id) to the
     * user identified by the given email.
     *
     * The status names considered "closed" (and therefore excluded from
     * the count) are passed as a parameter, since they can vary between
     * environments/installations (e.g. translated names).
     */
    public function assignedCount(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'closed_statuses' => ['nullable', 'array'],
            'closed_statuses.*' => ['string'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $closedStatuses = $request->input('closed_statuses', ['Resolved', 'Closed']);
        $closedStatusIds = TicketStatus::whereIn('name', $closedStatuses)->pluck('id');

        $count = $user->ticektResponsibility()
            ->whereNotIn('ticket_statuses_id', $closedStatusIds)
            ->count();

        return response()->json(['count' => $count]);
    }
}
