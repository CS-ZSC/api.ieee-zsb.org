<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventSponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EventSponsorController extends Controller
{
    //
    public function index()
    {
        return EventSponsor::all();
    }

    public function show($id)
    {
        return EventSponsor::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required',
            'logo' => 'image|mimes:jpg,jpeg,png|max:2048',
            'website_url' => 'nullable|url'
        ]);

        $path = null;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('sponsors', 'public');
        }

        $sponsor = EventSponsor::create([
            'event_id' => $request->event_id,
            'name' => $request->name,
            'logo' => $path,
            'website_url' => $request->website_url
        ]);

        return response()->json($sponsor);
    }

    public function update(Request $request, $id)
    {
        $sponsor = EventSponsor::findOrFail($id);

        $data = $request->except('_method');

        if ($request->hasFile('logo')) {
            if ($sponsor->logo) {
                Storage::disk('public')->delete($sponsor->logo);
            }

            $data['logo'] = $request->file('logo')->store('sponsors', 'public');
        }

        $sponsor->update($data);

        return response()->json($sponsor);
    }

    public function destroy($id)
    {
        $sponsor = EventSponsor::findOrFail($id);

        if ($sponsor->logo) {
            Storage::disk('public')->delete($sponsor->logo);
        }

        $sponsor->delete();

        return response()->json([
            "message" => "Deleted successfully"
        ]);
    }
}
