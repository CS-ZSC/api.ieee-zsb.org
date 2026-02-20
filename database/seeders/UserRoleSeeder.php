<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('name');

        $users = User::with(['groupable', 'track', 'positions'])->get();

        // Position → role mapping (lowercase position name → [role name, scope source])
        // scope source: 'groupable' = user's chapter/committee, 'track' = user's track, null = global
        $positionRoleMap = [
            'chairperson'                  => ['chapter chairperson', 'groupable'],
            'vice chairperson'             => ['vice chapter chairperson', 'groupable'],
            'technical vice chairperson'   => ['vice chapter chairperson', 'groupable'],
            'managerial vice chairperson'  => ['vice chapter chairperson', 'groupable'],
            'webmaster'                    => ['member', 'groupable'],
            'secretary'                    => ['member', 'groupable'],
            'treasurer'                    => ['member', 'groupable'],
            'lead'                         => null, // resolved dynamically based on groupable_type
            'vice lead'                    => null, // resolved dynamically based on groupable_type
            'track lead'                   => ['track leader', 'track'],
            'track vice-lead'              => ['vice track leader', 'track'],
        ];

        foreach ($users as $user) {
            // IEEE chapter members → ieee admin (global)
            if (
                $user->groupable_type === 'chapter' &&
                $user->groupable &&
                $user->groupable->short_name === 'IEEE'
            ) {
                if (isset($roles['ieee admin'])) {
                    $user->roles()->syncWithoutDetaching([
                        $roles['ieee admin']->id => [
                            'scopeable_type' => null,
                            'scopeable_id' => null,
                        ],
                    ]);
                }
                continue;
            }

            $positionNames = $user->positions->pluck('name')->map(fn ($n) => strtolower($n));

            foreach ($positionNames as $position) {
                $roleName = null;
                $scope = null;

                if ($position === 'lead') {
                    // Lead in committee → committee leader, Lead in chapter (WIE) → chapter chairperson
                    if ($user->groupable_type === 'committee') {
                        $roleName = 'committee leader';
                    } else {
                        $roleName = 'chapter chairperson';
                    }
                    $scope = $user->groupable;
                } elseif ($position === 'vice lead') {
                    if ($user->groupable_type === 'committee') {
                        $roleName = 'vice committee leader';
                    } else {
                        $roleName = 'vice chapter chairperson';
                    }
                    $scope = $user->groupable;
                } elseif (isset($positionRoleMap[$position])) {
                    [$roleName, $scopeSource] = $positionRoleMap[$position];
                    $scope = match ($scopeSource) {
                        'groupable' => $user->groupable,
                        'track' => $user->track,
                        default => null,
                    };
                } else {
                    $roleName = 'member';
                    $scope = $user->groupable;
                }

                if (!$roleName || !isset($roles[$roleName])) {
                    continue;
                }

                $user->roles()->syncWithoutDetaching([
                    $roles[$roleName]->id => [
                        'scopeable_type' => $scope?->getMorphClass(),
                        'scopeable_id' => $scope?->id,
                    ],
                ]);
            }
        }
    }
}
