<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        return response()->json(Goal::all());
    }

    public function store(Request $request)
    {
        return response()->json(Goal::create($request->all()), 201);
    }

    public function show($id)
    {
        return response()->json(Goal::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);
        $goal->update($request->all());
        return response()->json($goal);
    }

    public function destroy($id)
    {
        Goal::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
