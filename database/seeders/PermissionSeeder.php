<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Users
            'view users',
            'edit users',
            'delete users',

            // Chapters
            'view chapters',
            'create chapters',
            'edit chapters',
            'delete chapters',

            // Committees
            'view committees',
            'create committees',
            'edit committees',
            'delete committees',

            // Tracks
            'view tracks',
            'create tracks',
            'edit tracks',
            'delete tracks',

            // Events
            'view events',
            'create events',
            'edit events',
            'delete events',

            // Event Sub-resources
            'manage event images',
            'manage event participants',
            'manage speakers',
            'manage sponsors',

            // Competitions
            'view competitions',
            'create competitions',
            'edit competitions',
            'delete competitions',
            'manage competition prizes',

            // News
            'manage news',

            // Management
            'manage positions',
            'manage roles',
            'manage permissions',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }
    }
}
