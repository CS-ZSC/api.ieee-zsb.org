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
            PermissionSeeder::class,  // 1. Permissions must be created first
            RoleSeeder::class,        // 2. Then roles (depends on permissions)
            ChapterSeeder::class,     // 3. Chapters must be created before tracks
            TrackSeeder::class,       // 4. Then tracks (depends on chapters)
            CommitteeSeeder::class,   // 5. Then committees (independent)
            PositionSeeder::class,    // 6. Positions must exist before users
            UserSeeder::class,        // 7. Create users (depends on all above)
            DescriptionSeeder::class, // 8. Chapter descriptions
            SeasonSeeder::class,      // 9. Seasons (depends on chapters)
            SummarySeeder::class,     // 10. Summaries (depends on seasons)
            GoalSeeder::class,        // 11. Goals (depends on tracks & committees)
            ActivitySeeder::class,    // 12. Activities (depends on tracks & committees)
            NewsSeeder::class,        // 13. News with sections
            UserRoleSeeder::class,    // 14. Assign roles based on positions
        ]);

    }
}
