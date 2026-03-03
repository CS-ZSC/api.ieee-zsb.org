<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionParticipant;
use Illuminate\Http\Request;

class CompetitionParticipantController extends Controller
{
    public function index($competitionId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $participants = $competition->participants()->with('eventParticipant.user')->get();

        return response()->json([
            'message' => 'Competition participants',
            'data' => $participants
        ]);
    }

    public function store(Request $request, $competitionId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'event_participant_id' => 'required|integer|exists:event_participants,id',
        ]);

        $exists = CompetitionParticipant::where('competition_id', $competition->id)
            ->where('event_participant_id', $validated['event_participant_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Participant already registered in this competition',
                'data' => null
            ], 409);
        }

        $participant = CompetitionParticipant::create([
            'competition_id' => $competition->id,
            'event_participant_id' => $validated['event_participant_id'],
        ]);

        return response()->json([
            'message' => 'Participant registered successfully',
            'data' => $participant
        ], 201);
    }

    public function show($id)
    {
        $participant = CompetitionParticipant::with('eventParticipant.user', 'competition')->find($id);

        if (!$participant) {
            return response()->json([
                'message' => 'Competition participant not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Competition participant details',
            'data' => $participant
        ]);
    }

    public function destroy($id)
    {
        $participant = CompetitionParticipant::find($id);

        if (!$participant) {
            return response()->json([
                'message' => 'Competition participant not found',
                'data' => null
            ], 404);
        }

        $participant->delete();

        return response()->json([
            'message' => 'Participant removed successfully',
            'data' => null
        ]);
    }
}
