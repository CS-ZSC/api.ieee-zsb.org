<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // Positions linked to their default roles
        // null = handled by hardcoded mapping in assignDefaultRole() (context-dependent)
        $positions = [
            'Chairperson' => 'chapter chairperson',
            'Vice Chairperson' => 'vice chapter chairperson',
            'Technical Vice Chairperson' => 'vice chapter chairperson',
            'Managerial Vice Chairperson' => 'vice chapter chairperson',
            'Webmaster' => null,
            'Secretary' => null,
            'Treasurer' => null,
            'Lead' => null,       // committee leader OR chapter chairperson (depends on groupable_type)
            'Vice Lead' => null,  // vice committee leader OR vice chapter chairperson
            'Track Lead' => 'track leader',
            'Track Vice-Lead' => 'vice track leader',
            'Dev' => 'dev',        // Developer with full access
            'Visitor' => 'visitor', // EventsGate registered users
        ];

        foreach ($positions as $name => $roleName) {
            $roleId = $roleName ? Role::where('name', $roleName)->first()?->id : null;

            Position::updateOrCreate(
                ['name' => $name],
                ['role_id' => $roleId]
            );
        }
    }
}
