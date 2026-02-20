<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::withCount('users')->get();

        if ($positions->isEmpty()) {
            return response()->json([
                'message' => 'No positions found',
                'data' => []
            ], 200);
        }

        return response()->json($positions);
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermission('manage positions')) {
            abort(403, 'You do not have permission to manage positions.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:positions,name',
            'role_id' => 'nullable|integer|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $position = Position::create($validator->validated());

        return response()->json([
            'message' => 'Position created successfully',
            'data' => $position->load('role'),
        ], 201);
    }

    public function show($id)
    {
        $position = Position::with('users')->find($id);

        if (!$position) {
            return response()->json([
                'message' => 'Position not found',
                'data' => null
            ], 404);
        }

        return response()->json($position);
    }

    public function update(Request $request, Position $position)
    {
        if (!$request->user()->hasPermission('manage positions')) {
            abort(403, 'You do not have permission to manage positions.');
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('positions', 'name')->ignore($position->id),
            ],
            'role_id' => 'nullable|integer|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldRoleId = $position->role_id;

        $position->update($validator->validated());
        $position->refresh();

        // If role_id changed, recalculate roles for all users with this position
        if ($position->role_id !== $oldRoleId) {
            foreach ($position->users as $user) {
                $user->assignDefaultRole();
            }
        }

        return response()->json([
            'message' => 'Position updated successfully',
            'data' => $position->load('role'),
        ]);
    }

    public function destroy(Request $request, Position $position)
    {
        if (!$request->user()->hasPermission('manage positions')) {
            abort(403, 'You do not have permission to manage positions.');
        }

        $position->delete();

        return response()->json([
            'message' => 'Position deleted successfully',
        ], 200);
    }

    public function assignUser(Request $request, Position $position)
    {
        if (!$request->user()->hasPermission('manage positions')) {
            abort(403, 'You do not have permission to manage positions.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $validator->validated()['user_id'];

        if ($position->users()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'User already has this position.',
            ], 409);
        }

        $position->users()->attach($userId);

        // Recalculate user roles based on new positions
        $user = User::findOrFail($userId);
        $user->assignDefaultRole();

        return response()->json([
            'message' => 'Position assigned to user successfully',
            'data' => $position->load('users'),
        ], 201);
    }

    public function removeUser(Request $request, Position $position)
    {
        if (!$request->user()->hasPermission('manage positions')) {
            abort(403, 'You do not have permission to manage positions.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $validator->validated()['user_id'];

        if (!$position->users()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'User does not have this position.',
            ], 404);
        }

        $position->users()->detach($userId);

        // Recalculate user roles based on remaining positions
        $user = User::findOrFail($userId);
        $user->assignDefaultRole();

        return response()->json([
            'message' => 'Position removed from user successfully',
        ], 200);
    }
}
