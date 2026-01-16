<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
// In database/seeders/UserRoleSeeder.php

public function run(): void
{
    // Get all roles once
    $roles = Role::all()->keyBy('name');

    // Get all users with their relationships
    $users = User::with(['groupable', 'track'])->get();

    // Position to role mapping (all lowercase)
    $positionRoleMap = [
        'chairperson' => 'chapter chairperson',
        'vice chairperson' => 'vice chapter chairperson',
        'committee leader' => 'committee leader',
        'vice committee leader' => 'vice committee leader',
        'track leader' => 'track leader',
        'vice track leader' => 'vice track leader',
        'member' => 'member',
        'user' => 'member',
        'dev' => 'ieee admin'
    ];

    foreach ($users as $user) {
        $position = strtolower($user->position);

        // Get role name from map, default to 'member'
        $roleName = $positionRoleMap[$position] ?? 'member';

        // Skip if role doesn't exist
        if (!isset($roles[$roleName])) {
            continue;
        }

        $role = $roles[$roleName];

        // Determine the scope
        $scope = null;

        // For IEEE admins (dev or in IEEE chapter)
        if ($roleName === 'ieee admin' ||
            ($user->groupable_type === 'App\\Models\\Chapter' &&
             $user->groupable &&
             $user->groupable->short_name === 'IEEE')) {
            $scope = null; // Global scope
        }
        // For track-related roles
        elseif (in_array($roleName, ['track leader', 'vice track leader'])) {
            $scope = $user->track;
        }
        // For all other roles
        else {
            $scope = $user->groupable;
        }

        // Remove any existing roles
        $user->roles()->detach();

        // Assign the role with scope
        $user->roles()->attach($role->id, [
            'scopeable_type' => $scope ? get_class($scope) : null,
            'scopeable_id' => $scope ? $scope->id : null
        ]);
    }
}
}
