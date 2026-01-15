<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Chapter;

class UserController extends Controller
{
    /**
     * Get logged-in user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $user->load([
            'groupable' => function ($query) {
                // لو Chapter جيب التراك
                if ($query instanceof Chapter) {
                    $query->with('track');
                }
            }
        ]);

        return response()->json([
            'message' => 'User profile',
            'data' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'avatar'    => $user->avatar_src,
                'linkedin'  => $user->linkedin,
                'position'  => $user->position,

                'group' => $user->groupable ? [
                    'type' => class_basename($user->groupable_type),
                    'name' => $user->groupable->name,
                    'track' => $user->groupable instanceof Chapter
                        ? $user->groupable->track?->name
                        : null,
                ] : null,
            ]
        ]);
    }

    /**
     * Update user personal data (NOT position / password)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'       => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $user->id,
            'avatar_src' => 'sometimes|nullable|string',
            'linkedin'   => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // تحديث البيانات المسموحة فقط
        $user->update($validator->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data'    => $user->fresh()
        ]);
    }
}
