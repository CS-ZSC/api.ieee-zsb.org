<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Chapter;
use App\Models\Committee;
use App\Models\Track;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get logged-in user profile (Website)
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $user->load(['groupable', 'track', 'positions']);

        return response()->json([
            'message' => 'User profile',
            'data' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'avatar'    => $user->avatar_src,
                'linkedin'  => $user->linkedin,
                'positions' => $user->positions->pluck('name'),

                'group' => $user->groupable ? [
                    'type' => class_basename($user->groupable_type),
                    'name' => $user->groupable->name,
                ] : null,

                'track' => $user->track ? [
                    'id'   => $user->track->id,
                    'name' => $user->track->name,
                ] : null,
            ]
        ]);
    }

    /**
     * Update user personal data (Website)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'       => 'sometimes|string|max:255',
            'email'      => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar_src' => 'sometimes|nullable|string',
            'linkedin'   => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data'    => $user->fresh()
        ]);
    }

    /**
     * Get EventsGate visitor profile
     */
    public function eventsGateProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'EventsGate profile retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'national_id' => $user->national_id,
                'avatar_src' => $user->avatar_src,
                'linkedin' => $user->linkedin,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * Update EventsGate visitor profile
     */
    public function eventsGateUpdateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone_number' => ['sometimes', 'string', 'max:20', Rule::unique('users', 'phone_number')->ignore($user->id)],
            'national_id' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'national_id')->ignore($user->id)],
            'avatar_src' => 'sometimes|nullable|string',
            'linkedin' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'message' => 'EventsGate profile updated successfully',
            'data' => $user->fresh()
        ]);
    }




    //  (Dashboard)
    public function index()
    {
        $users = User::with([
            'positions.role',        // User positions with linked roles
            'roles',                // User roles with scope
            'groupable',            // Chapter/Committee assignment
            'track'                 // Track assignment
        ])->get();

        return response()->json([
            'message' => 'Users list',
            'data' => $users
        ]);
    }

    /**
     * Show single user (Dashboard)
     */
    public function show(User $user)
    {
        return response()->json([
            'message' => 'User details',
            'data' => $user->load([
                'positions.role',        // User positions with linked roles
                'roles',                // User roles with scope
                'groupable',            // Chapter/Committee assignment
                'track'                 // Track assignment
            ])
        ]);
    }


    /**
     * Update any user (Dashboard)
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'name'           => 'sometimes|string|max:255',
            'email'          => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'groupable_type' => 'sometimes|string|in:chapter,committee',
            'groupable_id'   => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $type = $request->input('groupable_type', $user->groupable_type);

                    // IEEE chapter rule
                    if ($user->groupable_type === 'chapter' && $user->groupable?->short_name === 'IEEE') {
                        if ($type !== 'chapter' || $value !== $user->groupable_id) {
                            $fail("Users in IEEE chapter cannot be moved to another chapter or committee.");
                        }
                    }

                    // Chapter exists
                    if ($type === 'chapter' && !Chapter::where('id', $value)->exists()) {
                        $fail("The selected chapter does not exist.");
                    }

                    // Committee exists
                    if ($type === 'committee' && !Committee::where('id', $value)->exists()) {
                        $fail("The selected committee does not exist.");
                    }

                    // Track validation when track_id is present
                    if ($request->has('track_id')) {
                        $trackId = $request->input('track_id');

                        // Track must belong to the chapter
                        if ($trackId && !Track::where('id', $trackId)->where('chapter_id', $value)->exists()) {
                            $fail("The selected track does not belong to the chosen chapter.");
                        }

                        // IEEE members cannot have a track
                        $ieeeChapterId = Chapter::where('short_name', 'IEEE')->first()?->id;
                        if ($type === 'chapter' && $value === $ieeeChapterId && $trackId !== null) {
                            $fail("IEEE members cannot have a track.");
                        }
                    }
                }
            ],
            'track_id'       => 'sometimes|nullable|integer|exists:tracks,id',
            'position_ids'   => 'sometimes|array',
            'position_ids.*' => 'integer|exists:positions,id',
            'avatar_src'     => 'sometimes|nullable|string',
            'linkedin'       => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Sync positions if provided
        if (isset($data['position_ids'])) {
            $user->positions()->sync($data['position_ids']);
            unset($data['position_ids']);
        }

        $user->update($data);
        $user->refresh();

        // Reassign role if groupable, track, or positions changed
        if (isset($data['groupable_type']) || isset($data['groupable_id']) || isset($data['track_id']) || $request->has('position_ids')) {
            $user->assignDefaultRole();
        }

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user->load([
                'positions.role',        // User positions with linked roles
                'roles',                // User roles with scope
                'groupable',            // Chapter/Committee assignment
                'track'                 // Track assignment
            ])
        ]);
    }



    /**
     * Delete user (Dashboard)
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
