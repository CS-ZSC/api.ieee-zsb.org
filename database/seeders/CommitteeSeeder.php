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
                'name' => 'Technical Committee',
                'hashtag' => '#TechTeam',
                'description' => 'Responsible for technical workshops and sessions',
                'image' => 'committees/tech-committee.jpg',
            ],
            [
                'name' => 'Public Relations',
                'hashtag' => '#PRTeam',
                'description' => 'Handles social media and public communications',
                'image' => 'committees/pr-committee.jpg',
            ],
            [
                'name' => 'Human Resources',
                'hashtag' => '#HRTeam',
                'description' => 'Manages member relations and internal affairs',
                'image' => 'committees/hr-committee.jpg',
            ],
            [
                'name' => 'Logistics',
                'hashtag' => '#LogisticsTeam',
                'description' => 'Handles event planning and logistics',
                'image' => 'committees/logistics-committee.jpg',
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
