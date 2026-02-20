<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\Summary;
use Illuminate\Database\Seeder;

class SummarySeeder extends Seeder
{
    public function run(): void
    {
        // Add a placeholder summary for each 2026 season
        $seasons = Season::where('year', 2026)->with('chapter')->get();

        foreach ($seasons as $season) {
            Summary::firstOrCreate(
                ['season_id' => $season->id],
                [
                    'summary_text' => "Season {$season->year} summary for {$season->chapter->name}.",
                    'season_id' => $season->id,
                ]
            );
        }
    }
}
