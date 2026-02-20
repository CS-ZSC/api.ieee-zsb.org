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
