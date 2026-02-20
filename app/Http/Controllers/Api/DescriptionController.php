<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Description;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DescriptionController extends Controller
{
    public function index()
    {
        $descriptions = Description::with('chapter')->get();

        if ($descriptions->isEmpty()) {
            return response()->json([
                'message' => 'No descriptions found',
                'data' => []
            ], 200);
        }

        return response()->json($descriptions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'about' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
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

        $description = Description::create($validator->validated());

        return response()->json([
            'message' => 'Description created successfully',
            'data' => $description->load('chapter'),
        ], 201);
    }

    public function show($id)
    {
        $description = Description::with('chapter')->find($id);

        if (!$description) {
            return response()->json([
                'message' => 'Description not found',
                'data' => null
            ], 404);
        }

        return response()->json($description);
    }

    public function update(Request $request, Description $description)
    {
        $this->authorize('update', $description->chapter);

        $validator = Validator::make($request->all(), [
            'about' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'chapter_id' => 'sometimes|required|exists:chapters,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $description->update($validator->validated());
        $description->refresh();

        return response()->json([
            'message' => 'Description updated successfully',
            'data' => $description->load('chapter'),
        ]);
    }

    public function destroy(Description $description)
    {
        $this->authorize('update', $description->chapter);

        $description->delete();

        return response()->json([
            'message' => 'Description deleted successfully',
        ], 200);
    }
}
