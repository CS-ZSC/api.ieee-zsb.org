<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order
        $this->call([
            ChapterSeeder::class,    // 1. Chapters must be created first
            TrackSeeder::class,      // 2. Then tracks (depends on chapters)
            CommitteeSeeder::class,  // 3. Then committees (independent)
            UserSeeder::class,       // 4. Finally, create users (depends on all above)
        ]);
        
    }
}
