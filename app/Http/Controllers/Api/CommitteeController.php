<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CommitteeController extends Controller
{
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
            'image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $committee = Committee::create($validator->validated());

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
                Rule::unique('committees', 'name')->ignore($committee->id),
            ],
            'hashtag' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $committee->update($validator->validated());
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

        $committee->delete();

        return response()->json([
            'message' => 'Committee deleted successfully',
        ], 200);
    }
}
