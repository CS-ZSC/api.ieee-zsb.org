<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
    |--------------------------------------------------------------------------
    | IEEE Admin (Global)
    |--------------------------------------------------------------------------
    */
        $ieeeAdmin = Role::firstOrCreate(
            ['name' => 'ieee admin'],
            ['scope_type' => 'global']
        );
        $ieeeAdmin->permissions()->sync(Permission::pluck('id'));
        /*
    |--------------------------------------------------------------------------
    | Chapter Chairperson
    |--------------------------------------------------------------------------
    */
        $chapterChair = Role::firstOrCreate(
            ['name' => 'chapter chairperson'],
            ['scope_type' => 'chapter']
        );
        $chapterChair->permissions()->sync(
            Permission::whereIn('name', [
                'view users',
                'edit users',
                'view chapters',
                'edit chapters',
                'view tracks',
                'create tracks',
                'edit tracks',
                'delete tracks',
            ])->pluck('id')
        );
        /*
    |--------------------------------------------------------------------------
    | Vice Chapter Chairperson
    |--------------------------------------------------------------------------
    */
        $viceChapterChair = Role::firstOrCreate(
            ['name' => 'vice chapter chairperson'],
            ['scope_type' => 'chapter']
        );
        $viceChapterChair->permissions()->sync(
            $chapterChair->permissions->pluck('id')
        );
        /*
    |--------------------------------------------------------------------------
    | Committee Leader
    |--------------------------------------------------------------------------
    */
        $committeeLeader = Role::firstOrCreate(
            ['name' => 'committee leader'],
            ['scope_type' => 'committee']
        );
        $committeeLeader->permissions()->sync(
            Permission::whereIn('name', [
                'view users',
                'view committees',
                'edit committees',
            ])->pluck('id')
        );
        /*
    |--------------------------------------------------------------------------
    | Vice Committee Leader
    |--------------------------------------------------------------------------
    */
        $viceCommitteeLeader = Role::firstOrCreate(
            ['name' => 'vice committee leader'],
            ['scope_type' => 'committee']
        );
        $viceCommitteeLeader->permissions()->sync(
            $committeeLeader->permissions->pluck('id')
        );
        /*
    |--------------------------------------------------------------------------
    | Track Leader
    |--------------------------------------------------------------------------
    */
        $trackLeader = Role::firstOrCreate(
            ['name' => 'track leader'],
            ['scope_type' => 'track']
        );
        $trackLeader->permissions()->sync(
            Permission::whereIn('name', [
                'view users',
                'view tracks',
                'edit tracks',
            ])->pluck('id')
        );
        /*
    |--------------------------------------------------------------------------
    | Vice Track Leader
    |--------------------------------------------------------------------------
    */
        $viceTrackLeader = Role::firstOrCreate(
            ['name' => 'vice track leader'],
            ['scope_type' => 'track']
        );
        $viceTrackLeader->permissions()->sync(
            $trackLeader->permissions->pluck('id')
        );
        /*
    |--------------------------------------------------------------------------
    | Dev (Full Access)
    |--------------------------------------------------------------------------
    */
        $dev = Role::firstOrCreate(
            ['name' => 'dev'],
            ['scope_type' => 'global']
        );
        $dev->permissions()->sync(Permission::pluck('id'));

        /*
    |--------------------------------------------------------------------------
    | Member (Basic)
    |--------------------------------------------------------------------------
    */
        $member = Role::firstOrCreate(
            ['name' => 'member'],
            ['scope_type' => 'global']
        );
        $member->permissions()->sync(
            Permission::whereIn('name', [
                'view chapters',
                'view committees',
                'view tracks',
            ])->pluck('id')
        );
    }
}
