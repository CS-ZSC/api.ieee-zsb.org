<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('activityable')->get();

        if ($activities->isEmpty()) {
            return response()->json([
                'message' => 'No activities found',
                'data' => []
            ], 200);
        }

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'activityable_type' => ['required', Rule::in(['track', 'committee'])],
            'activityable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $modelClass = Relation::getMorphedModel($data['activityable_type']);
        if (!$modelClass || !$modelClass::find($data['activityable_id'])) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['activityable_id' => ['The selected activityable does not exist.']],
            ], 422);
        }

        $parent = $modelClass::findOrFail($data['activityable_id']);
        $this->authorize('update', $parent);

        $activity = Activity::create($data);

        return response()->json([
            'message' => 'Activity created successfully',
            'data' => $activity->load('activityable'),
        ], 201);
    }

    public function show($id)
    {
        $activity = Activity::with('activityable')->find($id);

        if (!$activity) {
            return response()->json([
                'message' => 'Activity not found',
                'data' => null
            ], 404);
        }

        return response()->json($activity);
    }

    public function update(Request $request, Activity $activity)
    {
        $this->authorize('update', $activity->activityable);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'activityable_type' => ['sometimes', 'required', Rule::in(['track', 'committee'])],
            'activityable_id' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (isset($data['activityable_type']) && isset($data['activityable_id'])) {
            $modelClass = Relation::getMorphedModel($data['activityable_type']);
            if (!$modelClass || !$modelClass::find($data['activityable_id'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['activityable_id' => ['The selected activityable does not exist.']],
                ], 422);
            }
        }

        $activity->update($data);
        $activity->refresh();

        return response()->json([
            'message' => 'Activity updated successfully',
            'data' => $activity->load('activityable'),
        ]);
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('update', $activity->activityable);

        $activity->delete();

        return response()->json([
            'message' => 'Activity deleted successfully',
        ], 200);
    }
}
