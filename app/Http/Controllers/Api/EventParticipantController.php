<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Ticket;
use Illuminate\Http\Request;

class EventParticipantController extends Controller
{
    public function index($slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Event participants',
            'data' => $event->participants()->with('user')->get()
        ]);
    }

    /**
     * Admin: add a participant to an event.
     */
    public function store(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageParticipants', $event);

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|string|in:spectator,competitor',
        ]);

        $existing = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'User is already registered in this event',
                'data' => $existing
            ], 409);
        }

        $participant = EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Participant added successfully',
            'data' => $participant
        ]);
    }

    /**
     * Admin: remove a participant from an event.
     */
    public function destroy($slug, $participantId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageParticipants', $event);

        $participant = EventParticipant::where('id', $participantId)
            ->where('event_id', $event->id)
            ->first();

        if (!$participant) {
            return response()->json([
                'message' => 'Participant not found',
                'data' => null
            ], 404);
        }

        $participant->delete();

        return response()->json([
            'message' => 'Participant removed successfully',
            'data' => null
        ]);
    }

    /**
     * EventsGate: visitor registers themselves in an event.
     */
    public function registerUser(Request $request, $slug)
    {
        $user = $request->user();
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        // Only visitors can register
        if (!$user->positions()->where('name', 'Visitor')->exists()) {
            return response()->json([
                'message' => 'Only visitors can register for events',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'role' => 'required|string|in:spectator,competitor',
        ]);

        $existing = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You are already registered in this event',
                'data' => $existing
            ], 409);
        }

        $participant = EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'role' => $validated['role'],
        ]);

        Ticket::create([
            'event_participant_id' => $participant->id,
            'status' => 'pending'
        ]);    

        return response()->json([
            'message' => 'Registered successfully',
            'data' => $participant
        ]);
    }

    /**
     * EventsGate: visitor unregisters themselves from an event.
     */
    public function unregisterUser(Request $request, $slug)
    {
        $user = $request->user();
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return response()->json([
                'message' => 'You are not registered in this event',
                'data' => null
            ], 404);
        }

        $participant->delete();

        return response()->json([
            'message' => 'Unregistered successfully',
            'data' => null
        ]);
    }
}
