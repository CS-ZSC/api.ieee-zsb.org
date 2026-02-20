<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = [
            [
                'name' => 'Computer Society',
                'short_name' => 'CS',
                'logo' => 'chapters/cs-logo.png',
                'color_scheme_1' => '#00629B',
                'color_scheme_2' => '#FFFFFF',
            ],
            [
                'name' => 'Power & Energy Society',
                'short_name' => 'PES',
                'logo' => 'chapters/pes-logo.png',
                'color_scheme_1' => '#F2A900',
                'color_scheme_2' => '#000000',
            ],
            [
                'name' => 'Robotics and Automation Society',
                'short_name' => 'RAS',
                'logo' => 'chapters/ras-logo.png',
                'color_scheme_1' => '#E31937',
                'color_scheme_2' => '#FFFFFF',
            ],
            [
                'name' => 'Institute of Electrical and Electronics Engineers',
                'short_name' => 'IEEE',
                'logo' => 'chapters/ieee-logo.png',
                'color_scheme_1' => '#E31937',
                'color_scheme_2' => '#FFFFFF',
            ],
            [
                'name' => 'Women in Engineering',
                'short_name' => 'WIE',
                'logo' => 'chapters/wie-logo.png',
                'color_scheme_1' => '#E31937',
                'color_scheme_2' => '#FFFFFF',
            ]
        ];

        foreach ($chapters as $chapter) {
            Chapter::firstOrCreate(
                ['short_name' => $chapter['short_name']],
                $chapter
            );
        }
    }
}
