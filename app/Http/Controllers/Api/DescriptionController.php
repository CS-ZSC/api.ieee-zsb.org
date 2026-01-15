<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Description;
use Illuminate\Http\Request;

class DescriptionController extends Controller
{
    public function index()
    {
        $descriptions = Description::all();
        return response()->json($descriptions);
    }

    public function store(Request $request)
    {
        $description = Description::create($request->all());
        return response()->json($description, 201);
    }

    public function show(Description $description)
    {
        return response()->json($description);
    }

    public function update(Request $request, Description $description)
    {
        $description->update($request->all());
        return response()->json($description);
    }

    public function destroy(Description $description)
    {
        $description->delete();
        return response()->json(null, 204);
    }
}
