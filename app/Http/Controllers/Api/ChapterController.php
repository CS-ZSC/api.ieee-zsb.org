<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChapterController extends Controller
{
    public function index()
    {

        $chapters = Chapter::all();

        if ($chapters->isEmpty()) {
            return response()->json([
                'message' => 'No chapters found',
                'data' => []
            ], 200);
        }

        return response()->json($chapters);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255|unique:chapters,name',
            'short_name'     => 'required|string|max:50|unique:chapters,short_name',
            'logo'           => 'nullable|string',
            'color_scheme_1' => 'nullable|string|max:20',
            'color_scheme_2' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $chapter = Chapter::create($validator->validated());

        return response()->json([
            'message' => 'Chapter created successfully',
            'data'    => $chapter,
        ], 201);
    }

    public function show($id)
    {

        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json([
                'message' => 'Chapter not found',
                'data' => null
            ], 404);
        }

        return response()->json($chapter);
    }


public function update(Request $request, Chapter $chapter)
{
    $validator = Validator::make($request->all(), [
        'name' => [
            'sometimes',
            'required',
            'string',
            'max:255',
            Rule::unique('chapters', 'name')->ignore($chapter->id),
        ],
        'short_name' => [
            'sometimes',
            'required',
            'string',
            'max:50',
            Rule::unique('chapters', 'short_name')->ignore($chapter->id),
        ],
        'logo'           => 'nullable|string',
        'color_scheme_1' => 'nullable|string|max:20',
        'color_scheme_2' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $chapter->update($validator->validated());
    $chapter->refresh();

    return response()->json([
        'message' => 'Chapter updated successfully',
        'data'    => $chapter,
    ]);
}


    public function destroy(Chapter $chapter)
    {
        try {
            $chapter->delete();

            return response()->json([
                'message' => 'Chapter deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete chapter',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
