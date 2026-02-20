<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with('goalable')->get();

        if ($goals->isEmpty()) {
            return response()->json([
                'message' => 'No goals found',
                'data' => []
            ], 200);
        }

        return response()->json($goals);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goal' => 'required|string',
            'goalable_type' => ['required', Rule::in(['track', 'committee'])],
            'goalable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Resolve morph alias to actual class and verify existence
        $modelClass = Relation::getMorphedModel($data['goalable_type']);
        if (!$modelClass || !$modelClass::find($data['goalable_id'])) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['goalable_id' => ['The selected goalable does not exist.']],
            ], 422);
        }

        $parent = $modelClass::findOrFail($data['goalable_id']);
        $this->authorize('update', $parent);

        $goal = Goal::create($data);

        return response()->json([
            'message' => 'Goal created successfully',
            'data' => $goal->load('goalable'),
        ], 201);
    }

    public function show($id)
    {
        $goal = Goal::with('goalable')->find($id);

        if (!$goal) {
            return response()->json([
                'message' => 'Goal not found',
                'data' => null
            ], 404);
        }

        return response()->json($goal);
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal->goalable);

        $validator = Validator::make($request->all(), [
            'goal' => 'sometimes|required|string',
            'goalable_type' => ['sometimes', 'required', Rule::in(['track', 'committee'])],
            'goalable_id' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Verify the goalable exists if being changed
        if (isset($data['goalable_type']) && isset($data['goalable_id'])) {
            $modelClass = Relation::getMorphedModel($data['goalable_type']);
            if (!$modelClass || !$modelClass::find($data['goalable_id'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['goalable_id' => ['The selected goalable does not exist.']],
                ], 422);
            }
        }

        $goal->update($data);
        $goal->refresh();

        return response()->json([
            'message' => 'Goal updated successfully',
            'data' => $goal->load('goalable'),
        ]);
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('update', $goal->goalable);

        $goal->delete();

        return response()->json([
            'message' => 'Goal deleted successfully',
        ], 200);
    }
}
