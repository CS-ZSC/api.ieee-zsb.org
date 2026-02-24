<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::with('prizes')->get();
        return response()->json([
            'message' => 'Competitions list',
            'data' => $competitions
        ]);
    }

    public function show($id)
    {
        $competition = Competition::with('prizes')->find($id);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Competition details',
            'data' => $competition
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Competition::class);

        $validated = $request->validate([
            'event_id' => 'nullable|integer',
            'chapter_id' => 'nullable|integer',
            'name' => 'required|string',
            'overview' => 'nullable|string',
            'type' => 'required|in:individual,team',
            'max_team_members' => 'nullable|integer',
        ]);

        $competition = Competition::create($validated);
        return response()->json([
            'message' => 'Competition created successfully',
            'data' => $competition
        ]);
    }

    public function update(Request $request, $id)
    {
        $competition = Competition::find($id);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $this->authorize('update', $competition);

        $validated = $request->validate([
            'event_id' => 'sometimes|nullable|integer',
            'chapter_id' => 'sometimes|nullable|integer',
            'name' => 'sometimes|required|string',
            'overview' => 'nullable|string',
            'type' => 'sometimes|required|in:individual,team',
            'max_team_members' => 'nullable|integer',
        ]);

        $competition->update($validated);

        return response()->json([
            'message' => 'Competition updated successfully',
            'data' => $competition
        ]);
    }

    public function destroy($id)
    {
        $competition = Competition::find($id);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $this->authorize('delete', $competition);

        $competition->delete();

        return response()->json([
            'message' => 'Competition deleted successfully',
            'data' => null
        ]);
    }
}
