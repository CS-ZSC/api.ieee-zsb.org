<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            return response()->json([
                'message' => 'No permissions found',
                'data' => []
            ], 200);
        }

        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermission('manage permissions')) {
            abort(403, 'You do not have permission to manage permissions.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission = Permission::create($validator->validated());

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission,
        ], 201);
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->find($id);

        if (!$permission) {
            return response()->json([
                'message' => 'Permission not found',
                'data' => null
            ], 404);
        }

        return response()->json($permission);
    }

    public function update(Request $request, Permission $permission)
    {
        if (!$request->user()->hasPermission('manage permissions')) {
            abort(403, 'You do not have permission to manage permissions.');
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission->update($validator->validated());
        $permission->refresh();

        return response()->json([
            'message' => 'Permission updated successfully',
            'data' => $permission,
        ]);
    }

    public function destroy(Request $request, Permission $permission)
    {
        if (!$request->user()->hasPermission('manage permissions')) {
            abort(403, 'You do not have permission to manage permissions.');
        }

        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully',
        ], 200);
    }
}
