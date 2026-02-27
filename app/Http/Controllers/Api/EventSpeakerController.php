<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventSpeaker;
use Illuminate\Support\Facades\Storage;



class EventSpeakerController extends Controller
{
    //
    public function index()
    {
        $speakers = EventSpeaker::get();
        return response()->json($speakers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'linkedin_url' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048'
        ]);

        $eventId = $request->input('event_id');
        $event = Event::withoutGlobalScopes()->find($eventId);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ]);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('speakers', 'public');
        }

        $speaker = EventSpeaker::create([
            'event_id' => $request->event_id,
            'name' => $request->name,
            'email' => $request->email,
            'linkedin_url' => $request->linkedin_url,
            'bio' => $request->bio,
            'photo' => $photoPath
        ]);

        return response()->json($speaker);
    }

    public function show($id)
    {
        return EventSpeaker::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $speaker = EventSpeaker::findOrFail($id);

        $data = $request->except('_method');

        if ($request->hasFile('photo')) {
            if ($speaker->photo) {
                Storage::disk('public')->delete($speaker->photo);
            }

            $data['photo'] = $request->file('photo')->store('speakers', 'public');
        }

        $speaker->update($data);

        return response()->json($speaker);
    }

    public function destroy($id)
    {
        $speaker = EventSpeaker::findOrFail($id);

        if ($speaker->photo) {
            Storage::disk('public')->delete($speaker->photo);
        }

        $speaker->delete();

        return response()->json([
            'message' => 'Speaker deleted'
        ]);
    }
}
