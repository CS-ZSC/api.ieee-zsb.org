<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\Committee;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // One placeholder activity per track
        $tracks = Track::all();
        foreach ($tracks as $track) {
            $track->activities()->firstOrCreate(
                ['title' => "{$track->name} Workshop"],
                ['description' => "Introductory workshop for the {$track->name} track."]
            );
        }

        // One placeholder activity per committee
        $committees = Committee::all();
        foreach ($committees as $committee) {
            $committee->activities()->firstOrCreate(
                ['title' => "{$committee->name} Kickoff"],
                ['description' => "Season kickoff meeting for {$committee->name}."]
            );
        }
    }
}
