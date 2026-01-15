<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        return response()->json(Season::all());
    }

    public function store(Request $request)
    {
        return response()->json(Season::create($request->all()), 201);
    }

    public function show($id)
    {
        return response()->json(Season::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $season = Season::findOrFail($id);
        $season->update($request->all());
        return response()->json($season);
    }

    public function destroy($id)
    {
        Season::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
