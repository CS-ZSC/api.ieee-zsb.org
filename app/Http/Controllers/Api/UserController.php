<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Chapter;
use App\Models\Committee;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get logged-in user profile (Website)
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $user->load(['groupable', 'track']);

        return response()->json([
            'message' => 'User profile',
            'data' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'avatar'   => $user->avatar_src,
                'linkedin' => $user->linkedin,
                'position' => $user->position,

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
            'name'         => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|unique:users,email,' . $user->id,
            'phone_number' => 'sometimes|nullable|string|max:20|unique:users,phone_number,' . $user->id,
            'national_id'  => 'sometimes|nullable|string|max:50|unique:users,national_id,' . $user->id,
            'avatar_src'   => 'sometimes|nullable|string',
            'linkedin'     => 'sometimes|nullable|string',
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




    //  (Dashboard)
    public function index(Request $request)
    {
        $user = $request->user();

        $this->authorize('viewAny', User::class);

        // For IEEE Admin → sees all users
        if ($user->hasPermission('view users')) {
            $users = User::with('groupable')->get();
        } else {
            // Otherwise, limit by user's group scope
            $users = User::where('groupable_type', $user->groupable_type)
                ->where('groupable_id', $user->groupable_id)
                ->with('groupable')
                ->get();
        }

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
        $this->authorize('view', $user);

        return response()->json([
            'message' => 'User details',
            'data' => $user->load('groupable')
        ]);
    }


    /**
     * Update any user (Dashboard)
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:users,email,' . $user->id,
            'position'        => 'sometimes|string',
            'groupable_type'  => 'sometimes|string|in:chapter,committee',
            'groupable_id'    => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $type = $request->input('groupable_type', $user->groupable_type);
                    $position = strtolower($request->input('position', $user->position));

                    // IEEE chapter rule
                    if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
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

                    // Track rules: use has() instead of filled()
                    if ($request->has('track_id')) {
                        $trackId = $request->input('track_id');

                        // Only track leaders can have a track
                        if (!in_array($position, ['track leader', 'vice track leader', 'user'])) {
                            if ($trackId !== null) {
                                $fail("This position cannot have a track.");
                            }
                        }

                        // Track leader must have a valid track
                        if (in_array($position, ['track leader', 'vice track leader'])) {
                            if (!$trackId) {
                                $fail("Track Leader must have a track assigned.");
                            }
                            if ($type !== 'chapter') {
                                $fail("Track Leaders can only belong to a chapter.");
                            }

                            // Check track belongs to chapter
                            if ($trackId && !Track::where('id', $trackId)->where('chapter_id', $value)->exists()) {
                                $fail("The selected track does not belong to the chosen chapter.");
                            }
                        }

                        // IEEE members cannot have a track
                        $ieeeChapterId = Chapter::where('short_name', 'IEEE')->first()?->id;
                        if ($type === 'chapter' && $value === $ieeeChapterId && $trackId !== null) {
                            $fail("IEEE members cannot have a track.");
                        }
                    }
                }
            ],
            'track_id' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'avatar_src' => 'sometimes|nullable|string',
            'linkedin'   => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->refresh(); // Refresh attributes and relations

        // Reassign role if position, groupable, or track changed
        if (isset($data['position']) || isset($data['groupable_type']) || isset($data['groupable_id']) || isset($data['track_id'])) {
            $user->roles()->detach(); // Remove old roles
            $user->assignDefaultRole();
        }

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user->fresh()
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
