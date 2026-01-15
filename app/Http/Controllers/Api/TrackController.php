<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index()
    {
        return response()->json(Track::all());
    }

    public function store(Request $request)
    {
        return response()->json(Track::create($request->all()), 201);
    }

    public function show($id)
    {
        return response()->json(Track::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $track = Track::findOrFail($id);
        $track->update($request->all());
        return response()->json($track);
    }

    public function destroy($id)
    {
        Track::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
