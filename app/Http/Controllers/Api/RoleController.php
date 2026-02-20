<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();

        if ($roles->isEmpty()) {
            return response()->json([
                'message' => 'No roles found',
                'data' => []
            ], 200);
        }

        return response()->json($roles);
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'scope_type' => ['required', 'string', Rule::in(['global', 'chapter', 'committee', 'track'])],
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Check unique name + scope_type
        if (Role::where('name', $data['name'])->where('scope_type', $data['scope_type'])->exists()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['name' => ['A role with this name and scope type already exists.']],
            ], 422);
        }

        $role = Role::create([
            'name' => $data['name'],
            'scope_type' => $data['scope_type'],
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions'),
        ], 201);
    }

    public function show($id)
    {
        $role = Role::with(['permissions', 'userRoles.user', 'userRoles.scopeable'])->find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found',
                'data' => null
            ], 404);
        }

        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'scope_type' => ['sometimes', 'required', 'string', Rule::in(['global', 'chapter', 'committee', 'track'])],
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Check unique name + scope_type (ignoring self)
        $nameToCheck = $data['name'] ?? $role->name;
        $scopeToCheck = $data['scope_type'] ?? $role->scope_type;
        if (Role::where('name', $nameToCheck)->where('scope_type', $scopeToCheck)->where('id', '!=', $role->id)->exists()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['name' => ['A role with this name and scope type already exists.']],
            ], 422);
        }

        $role->update(collect($data)->only(['name', 'scope_type'])->toArray());

        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        $role->refresh();

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role->load('permissions'),
        ]);
    }

    public function destroy(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        if (in_array($role->name, ['ieee admin', 'member'])) {
            return response()->json([
                'message' => 'This role cannot be deleted.',
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ], 200);
    }

    public function syncPermissions(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role->permissions()->sync($validator->validated()['permissions']);

        return response()->json([
            'message' => 'Role permissions updated successfully',
            'data' => $role->load('permissions'),
        ]);
    }

    public function assignUser(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'scopeable_type' => ['nullable', 'string', Rule::in(['chapter', 'committee', 'track'])],
            'scopeable_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Validate scope consistency with role's scope_type
        if ($role->scope_type === 'global') {
            if (!empty($data['scopeable_type']) || !empty($data['scopeable_id'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['scopeable_type' => ['Global roles cannot have a scope.']],
                ], 422);
            }
        } else {
            if (empty($data['scopeable_type']) || empty($data['scopeable_id'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['scopeable_type' => ["This role requires a {$role->scope_type} scope."]],
                ], 422);
            }

            if ($data['scopeable_type'] !== $role->scope_type) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['scopeable_type' => ["Scope type must be '{$role->scope_type}' for this role."]],
                ], 422);
            }

            // Verify the scoped model exists
            $modelClass = Relation::getMorphedModel($data['scopeable_type']);
            if (!$modelClass || !$modelClass::find($data['scopeable_id'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['scopeable_id' => ['The selected scope does not exist.']],
                ], 422);
            }
        }

        $user = User::findOrFail($data['user_id']);
        $scope = null;

        if (!empty($data['scopeable_type']) && !empty($data['scopeable_id'])) {
            $modelClass = Relation::getMorphedModel($data['scopeable_type']);
            $scope = $modelClass::findOrFail($data['scopeable_id']);
        }

        // Check for duplicate
        $query = $user->roleAssignments()
            ->where('role_id', $role->id)
            ->where('scopeable_type', $scope ? $scope->getMorphClass() : null)
            ->where('scopeable_id', $scope ? $scope->id : null);

        if ($query->exists()) {
            return response()->json([
                'message' => 'User already has this role in this scope.',
            ], 409);
        }

        $userRole = $user->assignRole($role, $scope, manual: true);

        return response()->json([
            'message' => 'Role assigned to user successfully',
            'data' => $userRole->load(['user', 'role', 'scopeable']),
        ], 201);
    }

    public function removeUser(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'scopeable_type' => ['nullable', 'string', Rule::in(['chapter', 'committee', 'track'])],
            'scopeable_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $user = User::findOrFail($data['user_id']);
        $scope = null;

        if (!empty($data['scopeable_type']) && !empty($data['scopeable_id'])) {
            $modelClass = Relation::getMorphedModel($data['scopeable_type']);
            $scope = $modelClass::findOrFail($data['scopeable_id']);
        }

        $removed = $user->removeRole($role, $scope);

        if ($removed === 0) {
            return response()->json([
                'message' => 'User does not have this role in this scope.',
            ], 404);
        }

        return response()->json([
            'message' => 'Role removed from user successfully',
        ], 200);
    }
}
