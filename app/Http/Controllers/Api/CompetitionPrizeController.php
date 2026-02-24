<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionPrize;
use Illuminate\Http\Request;

class CompetitionPrizeController extends Controller
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

        return response()->json([
            'message' => 'Competition prizes',
            'data' => $competition->prizes
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

        $this->authorize('managePrizes', $competition);

        $validated = $request->validate([
            'title'             => 'required|string',
            'rank'              => 'required|integer',
            'prize_description' => 'nullable|string',
        ]);

        $validated['competition_id'] = $competition->id;
        $prize = CompetitionPrize::create($validated);

        return response()->json([
            'message' => 'Prize created successfully',
            'data' => $prize
        ]);
    }

    public function update(Request $request, $competitionId, $prizeId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $this->authorize('managePrizes', $competition);

        $prize = CompetitionPrize::where('id', $prizeId)
            ->where('competition_id', $competition->id)
            ->first();

        if (!$prize) {
            return response()->json([
                'message' => 'Prize not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'title'             => 'sometimes|required|string',
            'rank'              => 'sometimes|required|integer',
            'prize_description' => 'nullable|string',
        ]);

        $prize->update($validated);

        return response()->json([
            'message' => 'Prize updated successfully',
            'data' => $prize
        ]);
    }

    public function destroy($competitionId, $prizeId)
    {
        $competition = Competition::find($competitionId);

        if (!$competition) {
            return response()->json([
                'message' => 'Competition not found',
                'data' => null
            ], 404);
        }

        $this->authorize('managePrizes', $competition);

        $prize = CompetitionPrize::where('id', $prizeId)
            ->where('competition_id', $competition->id)
            ->first();

        if (!$prize) {
            return response()->json([
                'message' => 'Prize not found',
                'data' => null
            ], 404);
        }

        $prize->delete();

        return response()->json([
            'message' => 'Prize deleted successfully',
            'data' => null
        ]);
    }
}
