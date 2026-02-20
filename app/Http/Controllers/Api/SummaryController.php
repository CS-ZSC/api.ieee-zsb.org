<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SummaryController extends Controller
{
    public function index()
    {
        $summaries = Summary::with('season.chapter')->get();

        if ($summaries->isEmpty()) {
            return response()->json([
                'message' => 'No summaries found',
                'data' => []
            ], 200);
        }

        return response()->json($summaries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'summary_text' => 'required|string',
            'season_id' => 'required|exists:seasons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $season = Season::findOrFail($request->season_id);
        $this->authorize('update', $season->chapter);

        $summary = Summary::create($validator->validated());

        return response()->json([
            'message' => 'Summary created successfully',
            'data' => $summary->load('season'),
        ], 201);
    }

    public function show($id)
    {
        $summary = Summary::with('season.chapter')->find($id);

        if (!$summary) {
            return response()->json([
                'message' => 'Summary not found',
                'data' => null
            ], 404);
        }

        return response()->json($summary);
    }

    public function update(Request $request, Summary $summary)
    {
        $this->authorize('update', $summary->season->chapter);

        $validator = Validator::make($request->all(), [
            'summary_text' => 'sometimes|required|string',
            'season_id' => 'sometimes|required|exists:seasons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $summary->update($validator->validated());
        $summary->refresh();

        return response()->json([
            'message' => 'Summary updated successfully',
            'data' => $summary->load('season'),
        ]);
    }

    public function destroy(Summary $summary)
    {
        $this->authorize('update', $summary->season->chapter);

        $summary->delete();

        return response()->json([
            'message' => 'Summary deleted successfully',
        ], 200);
    }
}
