<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function index()
    {
        return response()->json(Committee::all());
    }

    public function store(Request $request)
    {
        return response()->json(Committee::create($request->all()), 201);
    }

    public function show($id)
    {
        return response()->json(Committee::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $committee = Committee::findOrFail($id);
        $committee->update($request->all());
        return response()->json($committee);
    }

    public function destroy($id)
    {
        Committee::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
