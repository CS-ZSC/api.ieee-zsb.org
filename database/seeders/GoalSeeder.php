<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\Committee;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        // Goals for each track
        $tracks = Track::all();
        foreach ($tracks as $track) {
            $track->goals()->firstOrCreate(
                ['goal' => "Develop skilled members in {$track->name}."]
            );
        }

        // Goals for each committee
        $committees = Committee::all();
        foreach ($committees as $committee) {
            $committee->goals()->firstOrCreate(
                ['goal' => "Achieve excellence in {$committee->name} operations."]
            );
        }
    }
}
