<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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


    // get information about user from ticket
    public function verifyUserTicket(Request $request)
    {
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
    
        if ($ticket->status !== 'new') {
            return response()->json([
                'message' => 'Ticket already used or invalid'
            ], 409);
        }
    
        $participant = $ticket->eventParticipant;
    
        return response()->json([
            'message' => 'Ticket valid',
            'data' => [
                'user' => $participant->user,
                'event' => $participant->event,
                'role' => $participant->role,
                'ticket_status' => $ticket->status
            ]
        ]);


        
    }

    // check in user after verifying info
    public function checkinUser(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|uuid'
        ]);

        $ticket = Ticket::where('qr_code', $request->qr_code)->first();

        if (!$ticket) {
            return response()->json([
                'message' => 'Invalid ticket'
            ], 404);
        }
    
        if ($ticket->status !== 'new') {
            return response()->json([
                'message' => 'Ticket already used or invalid'
            ], 409);
        }

        $ticket->status = 'checked_in';
        $ticket->save();

        return response()->json([
            'message' => 'Checked in successfully'
        ], 200);
    

        
    }

}
