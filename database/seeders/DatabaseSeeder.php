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
            UserSeeder::class,        // 6. Create users (depends on all above)
            UserRoleSeeder::class,    // 7. Assign roles to users (depends on all above)
        ]);

    }
}
