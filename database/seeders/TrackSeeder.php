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
            [
                'name' => 'Artificial Intelligence',
                'hashtag' => '#AITrack',
                'image' => 'tracks/ai-track.jpg',
                'description' => 'Explore the world of AI and machine learning',
                'chapter_id' => $csChapter->id,
            ],
            [
                'name' => 'Web Development',
                'hashtag' => '#WebDev',
                'image' => 'tracks/web-dev.jpg',
                'description' => 'Master full-stack web development',
                'chapter_id' => $csChapter->id,
            ],

            // PES Tracks
            [
                'name' => 'Renewable Energy',
                'hashtag' => '#RenewableEnergy',
                'image' => 'tracks/renewable-energy.jpg',
                'description' => 'Sustainable and renewable energy solutions',
                'chapter_id' => $pesChapter->id,
            ],

            // RAS Tracks
            [
                'name' => 'Robotics',
                'hashtag' => '#Robotics',
                'image' => 'tracks/robotics.jpg',
                'description' => 'Design and program autonomous robots',
                'chapter_id' => $rasChapter->id,
            ],
            [
                'name' => 'Automation',
                'hashtag' => '#Automation',
                'image' => 'tracks/automation.jpg',
                'description' => 'Industrial automation and control systems',
                'chapter_id' => $rasChapter->id,
            ],
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
