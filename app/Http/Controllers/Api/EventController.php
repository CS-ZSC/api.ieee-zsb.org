<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        return response()->json([
            'message' => 'Events list',
            'data' => $events
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:events,slug',
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'location' => 'required|string',
            'status' => 'required|string',
        ]);

        $event = Event::create($validated);
        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return response()->json([
            'message' => 'Event details',
            'data' => $event
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'slug' => 'sometimes|required|string|unique:events,slug,' . $event->id,
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
            'data' => null
        ]);
    }
}
