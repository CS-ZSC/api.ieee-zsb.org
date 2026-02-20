<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::with([
            'chapter',
            'goals',              // Track goals
            'activities',          // Track activities
            'users.positions',      // Users with their positions
            'users.roles'          // User roles with scope
        ])->get();

        if ($tracks->isEmpty()) {
            return response()->json([
                'message' => 'No tracks found',
                'data' => []
            ], 200);
        }

        return response()->json($tracks);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Track::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'hashtag' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $track = Track::create($validator->validated());

        return response()->json([
            'message' => 'Track created successfully',
            'data' => $track->load([
                'chapter',
                'goals',
                'activities',
                'users.positions',
                'users.roles'
            ]),
        ], 201);
    }

    public function show($id)
    {
        $track = Track::with([
            'chapter',
            'goals',              // Track goals
            'activities',          // Track activities
            'users.positions',      // Users with their positions
            'users.roles'          // User roles with scope
        ])->find($id);

        if (!$track) {
            return response()->json([
                'message' => 'Track not found',
                'data' => null
            ], 404);
        }

        return response()->json($track);
    }

    public function update(Request $request, Track $track)
    {
        $this->authorize('update', $track);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'hashtag' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'chapter_id' => 'sometimes|required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $track->update($validator->validated());
        $track->refresh();

        return response()->json([
            'message' => 'Track updated successfully',
            'data' => $track->load([
                'chapter',
                'goals',
                'activities',
                'users.positions',
                'users.roles'
            ]),
        ]);
    }

    public function destroy(Track $track)
    {
        $this->authorize('delete', $track);

        $track->delete();

        return response()->json([
            'message' => 'Track deleted successfully',
        ], 200);
    }
}
