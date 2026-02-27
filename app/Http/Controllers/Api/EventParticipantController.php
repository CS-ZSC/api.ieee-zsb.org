<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventParticipantController extends Controller
{

    public function registerUser(Request $request, $slug)
    {
        $user = $request->user();
        $event = Event::where('slug', $slug)->first();

        // check if event exists
        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $role = $validator->validated()['role'];
        $event->participants()->syncWithoutDetaching([
            $user->id => ['role' => $role]
        ]);
    
        return response()->json([
            'message' => 'Registered successfully',
            'event_id' => $event->id,
            'user_id' => $user->id,
            'role' => $role
        ]);
    
    }

    public function unregisterUser(Request $request, $slug)
    {
        $user = $request->user();
        $event = Event::where('slug', $slug)->first();

        //check if event exists
        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        //check if user is registered in this event
        if (!$event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is not registered in this event'
            ], 404);
        }

        //remove user from event participants
        $event->participants()->detach($user->id);

        return response()->json([
            'message' => 'Unregistered successfully',
            'event_id' => $event->id,
            'user_id' => $user->id
        ]);


    
    }
}
