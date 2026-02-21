<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageUploadTrait;

class TrackController extends Controller
{
    use ImageUploadTrait;
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
            'image' => $this->getImageValidationRules('logo'), // Track uses logo
            'description' => 'nullable|string',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'images/tracks');
        }

        $track = Track::create($data);

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
            'image' => $this->getImageValidationRules('logo'),
            'description' => 'nullable|string',
            'chapter_id' => 'sometimes|required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $this->deleteOldImage($track->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'images/tracks');
        }

        $track->update($data);
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

        $this->deleteOldImage($track->image);
        $track->delete();

        return response()->json([
            'message' => 'Track deleted successfully',
        ], 200);
    }
}
