<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = Chapter::all();

        // Create 2025 and 2026 seasons for each chapter
        foreach ($chapters as $chapter) {
            foreach ([2025, 2026] as $year) {
                Season::firstOrCreate(
                    ['year' => $year, 'chapter_id' => $chapter->id]
                );
            }
        }
    }
}
