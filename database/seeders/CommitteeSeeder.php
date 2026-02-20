<?php

namespace Database\Seeders;

use App\Models\Committee;
use Illuminate\Database\Seeder;

class CommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $committees = [
            [
                'name' => 'Ambassadors',
                'hashtag' => '#Ambassadors',
                'description' => null,
                'image' => null,
            ],
            [
                'name' => 'Business Development',
                'hashtag' => '#BizDev',
                'description' => null,
                'image' => null,
            ],
            [
                'name' => 'Multimedia',
                'hashtag' => '#Multimedia',
                'description' => null,
                'image' => null,
            ],
            [
                'name' => 'Operations',
                'hashtag' => '#Operations',
                'description' => null,
                'image' => null,
            ],
            [
                'name' => 'Talent & Tech',
                'hashtag' => '#TalentAndTech',
                'description' => null,
                'image' => null,
            ],
            [
                'name' => 'Marketing',
                'hashtag' => '#Marketing',
                'description' => null,
                'image' => null,
            ],
        ];

        foreach ($committees as $committee) {
            Committee::firstOrCreate(
                ['name' => $committee['name']],
                $committee
            );
        }
    }
}
