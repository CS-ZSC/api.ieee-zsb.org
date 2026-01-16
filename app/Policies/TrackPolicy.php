<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Track;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrackPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
            return true;
        }
    }

    public function view(User $user, Track $track)
    {
        return $user->hasPermission('view tracks', $track);
    }

    public function create(User $user)
    {
        return $user->hasPermission('create tracks');
    }

    public function update(User $user, Track $track)
    {
        return $user->hasPermission('edit tracks', $track);
    }

    public function delete(User $user, Track $track)
    {
        return $user->hasPermission('delete tracks', $track);
    }
}
