<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_id' => 'required|integer|exists:competitions,id',
            'leader_event_participant_id' => 'required|integer|exists:event_participants,id',
            'name' => 'required|string|max:255',
        ]);

        $validated['join_code'] = Team::generateUniqueJoinCode();

        $team = Team::create($validated);

        TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $validated['leader_event_participant_id'],
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'data' => $team
        ], 201);
    }

    public function joinWithCode(Request $request)
    {
        $validated = $request->validate([
            'join_code' => 'required|string',
            'event_participant_id' => 'required|integer|exists:event_participants,id',
        ]);

        $team = Team::where('join_code', $validated['join_code'])->first();

        if (!$team) {
            return response()->json([
                'message' => 'Invalid join code',
                'data' => null
            ], 404);
        }

        $exists = TeamMember::where('team_id', $team->id)
            ->where('event_participant_id', $validated['event_participant_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Participant already a member of this team',
                'data' => null
            ], 409);
        }

        $member = TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $validated['event_participant_id'],
        ]);

        return response()->json([
            'message' => 'Joined team successfully',
            'data' => $member
        ], 200);
    }
}
