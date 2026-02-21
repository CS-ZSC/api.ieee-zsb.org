<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventImageController extends Controller
{
    //
    public function index($eventSlug)
    {
        $event = Event::where('slug', $eventSlug)->firstOrFail();

        return response()->json([
            'message' => 'Event images',
            'data' => $event->images
        ]);
    }

    public function store(Request $request, $eventSlug)
    {
        $event = Event::where('slug', $eventSlug)->firstOrFail();

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $path = $request->file('image')->store('events', 'public');

        $image = EventImage::create([
            'event_id' => $event->id,
            'image_path' => $path
        ]);

        return response()->json([
            'message' => 'image uploaded',
            'data' => $image
        ]);
    }

    public function destroy($slug, $imageId)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $image = EventImage::where('id', $imageId)
            ->where('event_id', $event->id)
            ->firstOrFail();

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json([
            'message' => 'Image deleted'
        ]);
    }
}
