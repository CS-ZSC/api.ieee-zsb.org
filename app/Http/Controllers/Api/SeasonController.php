<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::with('chapter', 'summaries')->get();

        if ($seasons->isEmpty()) {
            return response()->json([
                'message' => 'No seasons found',
                'data' => []
            ], 200);
        }

        return response()->json($seasons);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|digits:4',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $chapter = Chapter::findOrFail($request->chapter_id);
        $this->authorize('update', $chapter);

        $season = Season::create($validator->validated());

        return response()->json([
            'message' => 'Season created successfully',
            'data' => $season->load('chapter'),
        ], 201);
    }

    public function show($id)
    {
        $season = Season::with('chapter', 'summaries')->find($id);

        if (!$season) {
            return response()->json([
                'message' => 'Season not found',
                'data' => null
            ], 404);
        }

        return response()->json($season);
    }

    public function update(Request $request, Season $season)
    {
        $this->authorize('update', $season->chapter);

        $validator = Validator::make($request->all(), [
            'year' => 'sometimes|required|digits:4',
            'chapter_id' => 'sometimes|required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $season->update($validator->validated());
        $season->refresh();

        return response()->json([
            'message' => 'Season updated successfully',
            'data' => $season->load('chapter'),
        ]);
    }

    public function destroy(Season $season)
    {
        $this->authorize('update', $season->chapter);

        $season->delete();

        return response()->json([
            'message' => 'Season deleted successfully',
        ], 200);
    }
}
