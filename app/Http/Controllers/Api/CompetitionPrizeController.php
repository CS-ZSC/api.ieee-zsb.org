<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompetitionPrize;
use Illuminate\Http\Request;

class CompetitionPrizeController extends Controller
{
    public function index($competitionId)
    {
        return CompetitionPrize::where('competition_id', $competitionId)->get();
    }

    public function show($id)
    {
        return CompetitionPrize::findOrFail($id);
    }

    public function store(Request $request, $competitionId)
    {
        $data = $request->validate([
            'title'             => 'required|string',
            'rank'              => 'required|integer',
            'prize_description' => 'nullable|string',
        ]);
        $data['competition_id'] = $competitionId;
        return CompetitionPrize::create($data);
    }

    public function update(Request $request, $id)
    {
        $prize = CompetitionPrize::findOrFail($id);
        $data = $request->validate([
            'title'             => 'required|string',
            'rank'              => 'required|integer',
            'prize_description' => 'nullable|string',
        ]);
        $prize->update($data);
        return $prize;
    }

    public function destroy($id)
    {
        CompetitionPrize::findOrFail($id)->delete();
        return response()->json(['message' => 'Prize deleted']);
    }
}
