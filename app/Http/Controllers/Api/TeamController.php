<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionParticipant;
use App\Models\EventParticipant;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // ─── EventsGate (Visitor self-service) ───────────────────────────────

    public function index($competitionId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $teams = $competition->teams()
            ->with('members.eventParticipant.user', 'leaderEventParticipant.user')
            ->get();

        return response()->json([
            'message' => 'Competition teams',
            'data' => $teams
        ]);
    }

    public function show($competitionId, $teamId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $team = Team::where('id', $teamId)
            ->where('competition_id', $competition->id)
            ->with('members.eventParticipant.user', 'leaderEventParticipant.user')
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Team details',
            'data' => $team
        ]);
    }

    public function createTeam(Request $request, $competitionId)
    {
        $user = $request->user();

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        if ($competition->type !== 'team') {
            return response()->json([
                'message' => 'This competition does not support teams',
                'data' => null
            ], 422);
        }

        $eventParticipant = EventParticipant::where('user_id', $user->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$eventParticipant) {
            return response()->json([
                'message' => 'You must be registered for this competition\'s event first',
                'data' => null
            ], 403);
        }

        // Check if user is already in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $eventParticipant->id)
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'You are already registered in an individual competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $eventParticipant->id)->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'You are already in a team for this event',
                'data' => null
            ], 409);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'competition_id' => $competition->id,
            'leader_event_participant_id' => $eventParticipant->id,
            'name' => $validated['name'],
            'join_code' => Team::generateUniqueJoinCode(),
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $eventParticipant->id,
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'data' => $team->load('members.eventParticipant.user')
        ], 201);
    }

    public function joinTeam(Request $request, $competitionId)
    {
        $user = $request->user();

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $eventParticipant = EventParticipant::where('user_id', $user->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$eventParticipant) {
            return response()->json([
                'message' => 'You must be registered for this competition\'s event first',
                'data' => null
            ], 403);
        }

        // Check if user is already in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $eventParticipant->id)
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'You are already registered in an individual competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $eventParticipant->id)->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'You are already in a team for this event',
                'data' => null
            ], 409);
        }

        $validated = $request->validate([
            'join_code' => 'required|string',
        ]);

        $team = Team::where('join_code', $validated['join_code'])
            ->where('competition_id', $competition->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'Invalid join code',
                'data' => null
            ], 404);
        }

        // Enforce max_team_members
        if ($competition->max_team_members && $team->members()->count() >= $competition->max_team_members) {
            return response()->json([
                'message' => 'Team is full (max ' . $competition->max_team_members . ' members)',
                'data' => null
            ], 422);
        }

        $member = TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $eventParticipant->id,
        ]);

        return response()->json([
            'message' => 'Joined team successfully',
            'data' => $team->load('members.eventParticipant.user')
        ]);
    }

    public function leaveTeam(Request $request, $competitionId)
    {
        $user = $request->user();

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $eventParticipant = EventParticipant::where('user_id', $user->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$eventParticipant) {
            return response()->json([
                'message' => 'You are not registered for this competition\'s event',
                'data' => null
            ], 404);
        }

        $member = TeamMember::whereHas('team', function ($q) use ($competition) {
            $q->where('competition_id', $competition->id);
        })->where('event_participant_id', $eventParticipant->id)->first();

        if (!$member) {
            return response()->json([
                'message' => 'You are not in any team for this competition',
                'data' => null
            ], 404);
        }

        $team = $member->team;
        $member->delete();

        // If no members left, delete the team
        if ($team->members()->count() === 0) {
            $team->delete();
            return response()->json([
                'message' => 'Left team successfully. Team was deleted (no members remaining)',
                'data' => null
            ]);
        }

        // If the leader left, reassign to the first remaining member
        if ($team->leader_event_participant_id === $eventParticipant->id) {
            $newLeader = $team->members()->first();
            $team->update(['leader_event_participant_id' => $newLeader->event_participant_id]);
        }

        return response()->json([
            'message' => 'Left team successfully',
            'data' => null
        ]);
    }

    public function addMember(Request $request, $competitionId)
    {
        $leader = $request->user();

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        // Verify the authenticated user is a team leader in this competition
        $leaderEventParticipant = EventParticipant::where('user_id', $leader->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$leaderEventParticipant) {
            return response()->json([
                'message' => 'You are not registered for this competition\'s event',
                'data' => null
            ], 403);
        }

        $team = Team::where('competition_id', $competition->id)
            ->where('leader_event_participant_id', $leaderEventParticipant->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'You are not a team leader in this competition',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'join_code' => 'required|string',
        ]);

        // Find the visitor by their user join_code
        $targetUser = User::where('join_code', $validated['join_code'])->first();

        if (!$targetUser) {
            return response()->json([
                'message' => 'Invalid join code',
                'data' => null
            ], 404);
        }

        // Check if the target user is registered for this event
        $targetEventParticipant = EventParticipant::where('user_id', $targetUser->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$targetEventParticipant) {
            return response()->json([
                'message' => 'This user is not registered for this competition\'s event',
                'data' => null
            ], 403);
        }

        // Check if user is already in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $targetEventParticipant->id)
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'This user is already registered in an individual competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $targetEventParticipant->id)->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'This user is already in a team for this event',
                'data' => null
            ], 409);
        }

        // Enforce max_team_members
        if ($competition->max_team_members && $team->members()->count() >= $competition->max_team_members) {
            return response()->json([
                'message' => 'Team is full (max ' . $competition->max_team_members . ' members)',
                'data' => null
            ], 422);
        }

        $member = TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $targetEventParticipant->id,
        ]);

        return response()->json([
            'message' => 'Member added to team successfully',
            'data' => $team->load('members.eventParticipant.user')
        ], 201);
    }

    public function removeMember(Request $request, $competitionId)
    {
        $leader = $request->user();

        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        // Verify that authenticated user is a team leader in this competition
        $leaderEventParticipant = EventParticipant::where('user_id', $leader->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$leaderEventParticipant) {
            return response()->json([
                'message' => 'You are not registered for this competition\'s event',
                'data' => null
            ], 403);
        }

        $team = Team::where('competition_id', $competition->id)
            ->where('leader_event_participant_id', $leaderEventParticipant->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'You are not a team leader in this competition',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'join_code' => 'required|string',
        ]);

        // Find member by their user join_code
        $targetUser = User::where('join_code', $validated['join_code'])->first();

        if (!$targetUser) {
            return response()->json([
                'message' => 'Invalid join code',
                'data' => null
            ], 404);
        }

        // Find the member record
        $targetEventParticipant = EventParticipant::where('user_id', $targetUser->id)
            ->where('event_id', $competition->event_id)
            ->first();

        if (!$targetEventParticipant) {
            return response()->json([
                'message' => 'This user is not registered for this competition\'s event',
                'data' => null
            ], 404);
        }

        $member = TeamMember::where('team_id', $team->id)
            ->where('event_participant_id', $targetEventParticipant->id)
            ->first();

        if (!$member) {
            return response()->json([
                'message' => 'This user is not a member of your team',
                'data' => null
            ], 404);
        }

        // Prevent leader from removing themselves
        if ($targetEventParticipant->id === $leaderEventParticipant->id) {
            return response()->json([
                'message' => 'You cannot remove yourself from the team. Use leave team endpoint instead.',
                'data' => null
            ], 422);
        }

        $member->delete();

        // If no members left, delete the team
        if ($team->members()->count() === 0) {
            $team->delete();
            return response()->json([
                'message' => 'Member removed successfully. Team was deleted (no members remaining)',
                'data' => null
            ]);
        }

        return response()->json([
            'message' => 'Member removed from team successfully',
            'data' => $team->load('members.eventParticipant.user')
        ]);
    }

    // ─── Dashboard (Admin) ───────────────────────────────────────────────

    public function adminStore(Request $request, $competitionId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'leader_event_participant_id' => 'required|integer|exists:event_participants,id',
        ]);

        // Check if the proposed leader is already in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $validated['leader_event_participant_id'])
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'This user is already registered in an individual competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $validated['leader_event_participant_id'])->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'This user is already in a team for this event',
                'data' => null
            ], 409);
        }

        $team = Team::create([
            'competition_id' => $competition->id,
            'leader_event_participant_id' => $validated['leader_event_participant_id'],
            'name' => $validated['name'],
            'join_code' => Team::generateUniqueJoinCode(),
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $validated['leader_event_participant_id'],
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'data' => $team->load('members.eventParticipant.user')
        ], 201);
    }

    public function adminDestroy($competitionId, $teamId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $team = Team::where('id', $teamId)
            ->where('competition_id', $competition->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
                'data' => null
            ], 404);
        }

        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully',
            'data' => null
        ]);
    }

    public function memberIndex($competitionId, $teamId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $team = Team::where('id', $teamId)
            ->where('competition_id', $competition->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
                'data' => null
            ], 404);
        }

        $members = $team->members()->with('eventParticipant.user')->get();

        return response()->json([
            'message' => 'Team members',
            'data' => $members
        ]);
    }

    public function memberStore(Request $request, $competitionId, $teamId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $team = Team::where('id', $teamId)
            ->where('competition_id', $competition->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'event_participant_id' => 'required|integer|exists:event_participants,id',
        ]);

        // Check if the participant is already in ANY competition for this event (individual or team)
        $eventCompetitionIds = Competition::where('event_id', $competition->event_id)->pluck('id');

        $inIndividual = CompetitionParticipant::whereIn('competition_id', $eventCompetitionIds)
            ->where('event_participant_id', $validated['event_participant_id'])
            ->exists();

        if ($inIndividual) {
            return response()->json([
                'message' => 'This user is already registered in an individual competition for this event',
                'data' => null
            ], 409);
        }

        $inTeam = TeamMember::whereHas('team', function ($q) use ($eventCompetitionIds) {
            $q->whereIn('competition_id', $eventCompetitionIds);
        })->where('event_participant_id', $validated['event_participant_id'])->exists();

        if ($inTeam) {
            return response()->json([
                'message' => 'This user is already in a team for this event',
                'data' => null
            ], 409);
        }

        $exists = TeamMember::where('team_id', $team->id)
            ->where('event_participant_id', $validated['event_participant_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Participant is already a member of this team',
                'data' => null
            ], 409);
        }

        if ($competition->max_team_members && $team->members()->count() >= $competition->max_team_members) {
            return response()->json([
                'message' => 'Team is full (max ' . $competition->max_team_members . ' members)',
                'data' => null
            ], 422);
        }

        $member = TeamMember::create([
            'team_id' => $team->id,
            'event_participant_id' => $validated['event_participant_id'],
        ]);

        return response()->json([
            'message' => 'Member added to team successfully',
            'data' => $member->load('eventParticipant.user')
        ], 201);
    }

    public function memberDestroy($competitionId, $teamId, $memberId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $team = Team::where('id', $teamId)
            ->where('competition_id', $competition->id)
            ->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
                'data' => null
            ], 404);
        }

        $member = TeamMember::where('id', $memberId)
            ->where('team_id', $team->id)
            ->first();

        if (!$member) {
            return response()->json([
                'message' => 'Team member not found',
                'data' => null
            ], 404);
        }

        $member->delete();

        return response()->json([
            'message' => 'Member removed from team successfully',
            'data' => null
        ]);
    }
}
