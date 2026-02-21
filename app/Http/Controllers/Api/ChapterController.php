<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ImageUploadTrait;

class ChapterController extends Controller
{
    use ImageUploadTrait;
    public function index()
    {

        $chapters = Chapter::with(['tracks.goals', 'tracks.activities', 'tracks.users.positions', 'descriptions', 'seasons', 'users' => fn ($q) => $q->whereNull('track_id')->with('positions')])->get();

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
        $this->authorize('create', Chapter::class);

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255|unique:chapters,name',
            'short_name'     => 'required|string|max:50|unique:chapters,short_name',
            'logo'           => $this->getImageValidationRules('logo'), // Chapter uses logo
            'color_scheme_1' => 'nullable|string|max:20',
            'color_scheme_2' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadImage($request->file('logo'), 'images/chapters');
        }

        $chapter = Chapter::create($data);

        return response()->json([
            'message' => 'Chapter created successfully',
            'data'    => $chapter,
        ], 201);
    }

    public function show($id)
    {

        $chapter = Chapter::with(['tracks.goals', 'tracks.activities', 'tracks.users.positions', 'descriptions', 'seasons', 'users' => fn ($q) => $q->whereNull('track_id')->with('positions')])->find($id);

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
        $this->authorize('update', $chapter);

        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('images/chapters', 'name')->ignore($chapter->id),
            ],
            'short_name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('images/chapters', 'short_name')->ignore($chapter->id),
            ],
            'logo'           => $this->getImageValidationRules('logo'),
            'color_scheme_1' => 'nullable|string|max:20',
            'color_scheme_2' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $this->deleteOldImage($chapter->logo);
            $data['logo'] = $this->uploadImage($request->file('logo'), 'images/chapters');
        }

        $chapter->update($data);
        $chapter->refresh();

        return response()->json([
            'message' => 'Chapter updated successfully',
            'data'    => $chapter,
        ]);
    }


    public function destroy(Chapter $chapter)
    {
        $this->authorize('delete', $chapter);

        try {
            $this->deleteOldImage($chapter->logo);
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
