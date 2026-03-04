<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionParticipant;
use App\Models\TeamMember;
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

        $this->authorize('manageParticipants', $competition);

        $validated = $request->validate([
            'event_participant_id' => 'required|integer|exists:event_participants,id',
        ]);

        // Check if the event_participant belongs to the same event as the competition
        $eventParticipant = \App\Models\EventParticipant::find($validated['event_participant_id']);

        if ($eventParticipant->event_id !== $competition->event_id) {
            return response()->json([
                'message' => 'You must be registered for this competition\'s event first',
                'data' => null
            ], 403);
        }

        // Check if already in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $validated['event_participant_id'])
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'Participant is already registered in a competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $validated['event_participant_id'])->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'Participant is already in a team competition for this event',
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

    public function show($competitionId, $participantId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $participant = CompetitionParticipant::where('id', $participantId)
            ->where('competition_id', $competition->id)
            ->with('eventParticipant.user', 'competition')
            ->first();

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

    public function destroy($competitionId, $participantId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageParticipants', $competition);

        $participant = CompetitionParticipant::where('id', $participantId)
            ->where('competition_id', $competition->id)
            ->first();

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

    public function registerUser(Request $request, $competitionId)
    {
        $user = $request->user();

        // Check if user is a visitor
        if (!$user->positions()->where('name', 'Visitor')->exists()) {
            return response()->json([
                'message' => 'Only visitors can register for competitions',
                'data' => null
            ], 403);
        }

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        if ($competition->type === 'team') {
            return response()->json([
                'message' => 'This is a team competition. Use the teams endpoint to create or join a team',
                'data' => null
            ], 422);
        }

        // Check if user is registered for the competition's event
        $eventParticipant = \App\Models\EventParticipant::where('user_id', $user->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$eventParticipant) {
            return response()->json([
                'message' => 'You must be registered for this competition\'s event first',
                'data' => null
            ], 403);
        }

        // Check if already registered in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $eventParticipant->id)
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'You are already registered in a competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $eventParticipant->id)->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'You are already in a team competition for this event',
                'data' => null
            ], 409);
        }

        $participant = CompetitionParticipant::create([
            'competition_id' => $competition->id,
            'event_participant_id' => $eventParticipant->id,
        ]);

        return response()->json([
            'message' => 'Successfully registered for competition',
            'data' => $participant->load('eventParticipant.user')
        ], 201);
    }

    public function unregisterUser(Request $request, $competitionId)
    {
        $user = $request->user();

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        // Find the user's event participant record for this event
        $eventParticipant = \App\Models\EventParticipant::where('user_id', $user->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$eventParticipant) {
            return response()->json([
                'message' => 'You are not registered for this competition\'s event',
                'data' => null
            ], 404);
        }

        // Find and delete the competition participant record
        $participant = CompetitionParticipant::where('competition_id', $competition->id)
            ->where('event_participant_id', $eventParticipant->id)
            ->first();

        if (!$participant) {
            return response()->json([
                'message' => 'You are not registered for this competition',
                'data' => null
            ], 404);
        }

        $participant->delete();

        return response()->json([
            'message' => 'Successfully unregistered from competition',
            'data' => null
        ]);
    }
}
