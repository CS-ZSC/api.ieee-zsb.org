<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSpeaker;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;

class EventSpeakerController extends Controller
{
    use ImageUploadTrait;
    public function index($slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Event speakers',
            'data' => $event->speakers
        ]);
    }

    public function show($slug, $speakerId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $speaker = EventSpeaker::where('id', $speakerId)
            ->where('event_id', $event->id)
            ->first();

        if (!$speaker) {
            return response()->json([
                'message' => 'Speaker not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Speaker details',
            'data' => $speaker
        ]);
    }

    public function store(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageSpeakers', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'linkedin_url' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo' => $this->getImageValidationRules('default'),
        ]);

        $data = $validated;
        $data['event_id'] = $event->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadImage($request->file('photo'), 'images/speakers');
        }

        $speaker = EventSpeaker::create($data);

        return response()->json([
            'message' => 'Speaker created successfully',
            'data' => $speaker
        ]);
    }

    public function update(Request $request, $slug, $speakerId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageSpeakers', $event);

        $speaker = EventSpeaker::where('id', $speakerId)
            ->where('event_id', $event->id)
            ->first();

        if (!$speaker) {
            return response()->json([
                'message' => 'Speaker not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email',
            'linkedin_url' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo' => $this->getImageValidationRules('default'),
        ]);

        $data = $validated;

        if ($request->hasFile('photo')) {
            $this->deleteOldImage($speaker->photo);
            $data['photo'] = $this->uploadImage($request->file('photo'), 'images/speakers');
        }

        $speaker->update($data);

        return response()->json([
            'message' => 'Speaker updated successfully',
            'data' => $speaker
        ]);
    }

    public function destroy($slug, $speakerId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageSpeakers', $event);

        $speaker = EventSpeaker::where('id', $speakerId)
            ->where('event_id', $event->id)
            ->first();

        if (!$speaker) {
            return response()->json([
                'message' => 'Speaker not found',
                'data' => null
            ], 404);
        }

        $this->deleteOldImage($speaker->photo);

        $speaker->delete();

        return response()->json([
            'message' => 'Speaker deleted successfully',
            'data' => null
        ]);
    }
}
