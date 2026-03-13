<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompetitionParticipant;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function getUserTickets(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::whereHas('eventParticipant', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get()
        ->makeHidden(['event_participant_id']);

        return response()->json([
            'message' => 'Tickets list',
            'data' => $tickets
        ]);
    }

    public function verifyUserTicket(Request $request)
    {
        $this->authorize('manageTickets', Event::class);

        $request->validate([
            'qr_code' => 'required|uuid'
        ]);

        $ticket = Ticket::where('qr_code', $request->qr_code)
            ->with('eventParticipant.user', 'eventParticipant.event')
            ->first();

        if (!$ticket) {
            return response()->json([
                'message' => 'Invalid ticket'
            ], 404);
        }

        if ($ticket->status !== 'pending') {
            return response()->json([
                'message' => 'Ticket already verified, checked in, or invalid',
                'data' => ['ticket_status' => $ticket->status]
            ], 409);
        }

        $ticket->status = 'verified';
        $ticket->save();

        $participant = $ticket->eventParticipant;

        $competitions = CompetitionParticipant::where('event_participant_id', $participant->id)
            ->with('competition')
            ->get()
            ->pluck('competition');

        $data = [
            'user' => $participant->user,
            'event' => $participant->event,
            'role' => $participant->role,
            'ticket_status' => $ticket->status
        ];

        if ($competitions->isNotEmpty()) {
            $data['competitions'] = $competitions;
        }

        return response()->json([
            'message' => 'Ticket verified',
            'data' => $data
        ]);
    }

    public function checkinUser(Request $request)
    {
        $this->authorize('manageTickets', Event::class);

        $request->validate([
            'qr_code' => 'required|uuid'
        ]);

        $ticket = Ticket::where('qr_code', $request->qr_code)->first();

        if (!$ticket) {
            return response()->json([
                'message' => 'Invalid ticket'
            ], 404);
        }

        if ($ticket->status !== 'verified') {
            return response()->json([
                'message' => $ticket->status === 'checked_in'
                    ? 'Ticket already checked in'
                    : 'Ticket must be verified before check-in',
                'data' => ['ticket_status' => $ticket->status]
            ], 409);
        }

        $ticket->status = 'checked_in';
        $ticket->save();

        return response()->json([
            'message' => 'Checked in successfully'
        ], 200);
    }
}
