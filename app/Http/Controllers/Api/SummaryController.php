<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Summary;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index()
    {
        return response()->json(Summary::all());
    }

    public function store(Request $request)
    {
        return response()->json(Summary::create($request->all()), 201);
    }

    public function show($id)
    {
        return response()->json(Summary::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $summary = Summary::findOrFail($id);
        $summary->update($request->all());
        return response()->json($summary);
    }

    public function destroy($id)
    {
        Summary::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
