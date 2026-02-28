<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSponsor;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;

class EventSponsorController extends Controller
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
            'message' => 'Event sponsors',
            'data' => $event->sponsors
        ]);
    }

    public function show($slug, $sponsorId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $sponsor = EventSponsor::where('id', $sponsorId)
            ->where('event_id', $event->id)
            ->first();

        if (!$sponsor) {
            return response()->json([
                'message' => 'Sponsor not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Sponsor details',
            'data' => $sponsor
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

        $this->authorize('manageSponsors', $event);

        $validated = $request->validate([
            'name' => 'required|string',
            'logo' => $this->getImageValidationRules('logo'),
            'website_url' => 'nullable|url',
        ]);

        $data = $validated;
        $data['event_id'] = $event->id;

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadImage($request->file('logo'), 'images/sponsors');
        }

        $sponsor = EventSponsor::create($data);

        return response()->json([
            'message' => 'Sponsor created successfully',
            'data' => $sponsor
        ]);
    }

    public function update(Request $request, $slug, $sponsorId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageSponsors', $event);

        $sponsor = EventSponsor::where('id', $sponsorId)
            ->where('event_id', $event->id)
            ->first();

        if (!$sponsor) {
            return response()->json([
                'message' => 'Sponsor not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'logo' => $this->getImageValidationRules('logo'),
            'website_url' => 'nullable|url',
        ]);

        $data = $validated;

        if ($request->hasFile('logo')) {
            $this->deleteOldImage($sponsor->logo);
            $data['logo'] = $this->uploadImage($request->file('logo'), 'images/sponsors');
        }

        $sponsor->update($data);

        return response()->json([
            'message' => 'Sponsor updated successfully',
            'data' => $sponsor
        ]);
    }

    public function destroy($slug, $sponsorId)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
                'data' => null
            ], 404);
        }

        $this->authorize('manageSponsors', $event);

        $sponsor = EventSponsor::where('id', $sponsorId)
            ->where('event_id', $event->id)
            ->first();

        if (!$sponsor) {
            return response()->json([
                'message' => 'Sponsor not found',
                'data' => null
            ], 404);
        }

        $this->deleteOldImage($sponsor->logo);

        $sponsor->delete();

        return response()->json([
            'message' => 'Sponsor deleted successfully',
            'data' => null
        ]);
    }
}
