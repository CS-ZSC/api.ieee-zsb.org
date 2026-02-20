<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Track;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    public function run(): void
    {
        // Get chapters to associate tracks with
        $csChapter = Chapter::where('short_name', 'CS')->firstOrFail();
        $pesChapter = Chapter::where('short_name', 'PES')->firstOrFail();
        $rasChapter = Chapter::where('short_name', 'RAS')->firstOrFail();

        $tracks = [
            // Computer Society Tracks
            ['name' => 'Frontend', 'chapter_id' => $csChapter->id],
            ['name' => 'Backend', 'chapter_id' => $csChapter->id],
            ['name' => 'Artificial Intelligence', 'chapter_id' => $csChapter->id],
            ['name' => 'Cyber Security', 'chapter_id' => $csChapter->id],
            ['name' => 'Data Science', 'chapter_id' => $csChapter->id],
            ['name' => 'Mobile', 'chapter_id' => $csChapter->id],

            // PES Tracks
            ['name' => 'Basic Automation', 'chapter_id' => $pesChapter->id],
            ['name' => 'Advanced Automation', 'chapter_id' => $pesChapter->id],
            ['name' => 'Smart Home', 'chapter_id' => $pesChapter->id],
            ['name' => 'E-Mobility', 'chapter_id' => $pesChapter->id],
            ['name' => 'Distribution', 'chapter_id' => $pesChapter->id],

            // RAS Tracks
            ['name' => 'Mechanical Design', 'chapter_id' => $rasChapter->id],
            ['name' => 'PCB Design', 'chapter_id' => $rasChapter->id],
            ['name' => 'Embedded Systems', 'chapter_id' => $rasChapter->id],
            ['name' => 'ROS', 'chapter_id' => $rasChapter->id],
            ['name' => 'IC Design', 'chapter_id' => $rasChapter->id],
        ];

        foreach ($tracks as $track) {
            Track::firstOrCreate(
                [
                    'name' => $track['name'],
                    'chapter_id' => $track['chapter_id']
                ],
                $track
            );
        }
    }
}
