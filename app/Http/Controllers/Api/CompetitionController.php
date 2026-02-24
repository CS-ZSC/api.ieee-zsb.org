<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index()
    {
        return Competition::with('prizes')->get();
    }

    public function show($id)
    {
        return Competition::with('prizes')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'nullable|integer',
            'chapter_id' => 'nullable|integer',
            'name' => 'required|string',
            'overview' => 'nullable|string',
            'type' => 'required|in:individual,team',
            'max_team_members' => 'nullable|integer',
        ]);
        $competition = Competition::create($data);
        return $competition;
    }

    public function update(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);
        $data = $request->validate([
            'event_id' => 'nullable|integer',
            'chapter_id' => 'nullable|integer',
            'name' => 'required|string',
            'overview' => 'nullable|string',
            'type' => 'required|in:individual,team',
            'max_team_members' => 'nullable|integer',
        ]);
        $competition->update($data);
        return $competition;
    }

    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);
        $competition->delete();
        return response()->json(['message' => 'Competition deleted']);
    }
}
