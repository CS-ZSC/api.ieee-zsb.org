<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Traits\ImageUploadTrait;

class EventController extends Controller
{
    use ImageUploadTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('images')->get();
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
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:events,slug',
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => $this->getImageValidationRules('logo'),
            'cover_image' => $this->getImageValidationRules('cover'),
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'location' => 'required|string',
            'status' => 'required|string',
        ]);

        $data = $validated;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadImage($request->file('logo'), 'images/events');
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->uploadImage($request->file('cover_image'), 'images/events');
        }

        $event = Event::create($data);
        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $event = Event::with('images')->where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Event details',
            'data' => $event
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'slug' => 'sometimes|required|string|unique:events,slug,' . $event->id,
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => $this->getImageValidationRules('logo'),
            'cover_image' => $this->getImageValidationRules('cover'),
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
        ]);

        $data = $validated;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $this->deleteOldImage($event->logo);
            $data['logo'] = $this->uploadImage($request->file('logo'), 'images/events');
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $this->deleteOldImage($event->cover_image);
            $data['cover_image'] = $this->uploadImage($request->file('cover_image'), 'images/events');
        }

        $event->update($data);

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('delete', $event);

        $this->deleteOldImage($event->logo);
        $this->deleteOldImage($event->cover_image);
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
            'data' => null
        ]);
    }
}
