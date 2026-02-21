<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ImageUploadTrait;

class CommitteeController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $committees = Committee::with([
            'goals',              // Committee goals
            'activities',          // Committee activities
            'users.positions',      // Users with their positions
            'users.roles'          // User roles with scope
        ])->get();

        if ($committees->isEmpty()) {
            return response()->json([
                'message' => 'No committees found',
                'data' => []
            ], 200);
        }

        return response()->json($committees);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Committee::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:committees,name',
            'hashtag' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => $this->getImageValidationRules('logo'), // Committee uses logo
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
            $data['image'] = $this->uploadImage($request->file('image'), 'images/committees');
        }

        $committee = Committee::create($data);

        return response()->json([
            'message' => 'Committee created successfully',
            'data' => $committee->load([
                'goals',
                'activities',
                'users.positions',
                'users.roles'
            ]),
        ], 201);
    }

    public function show($id)
    {
        $committee = Committee::with([
            'goals',              // Committee goals
            'activities',          // Committee activities
            'users.positions',      // Users with their positions
            'users.roles'          // User roles with scope
        ])->find($id);

        if (!$committee) {
            return response()->json([
                'message' => 'Committee not found',
                'data' => null
            ], 404);
        }

        return response()->json($committee);
    }

    public function update(Request $request, Committee $committee)
    {
        $this->authorize('update', $committee);

        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('images/committees', 'name')->ignore($committee->id),
            ],
            'hashtag' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => $this->getImageValidationRules('logo'),
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
            $this->deleteOldImage($committee->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'images/committees');
        }

        $committee->update($data);
        $committee->refresh();

        return response()->json([
            'message' => 'Committee updated successfully',
            'data' => $committee->load([
                'goals',
                'activities',
                'users.positions',
                'users.roles'
            ]),
        ]);
    }

    public function destroy(Committee $committee)
    {
        $this->authorize('delete', $committee);

        $this->deleteOldImage($committee->image);
        $committee->delete();

        return response()->json([
            'message' => 'Committee deleted successfully',
        ], 200);
    }
}
