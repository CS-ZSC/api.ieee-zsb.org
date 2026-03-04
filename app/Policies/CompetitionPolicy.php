<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Competition;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
            return true;
        }
    }

    public function view(User $user, Competition $competition)
    {
        return $user->hasPermission('view competitions');
    }

    public function create(User $user)
    {
        return $user->hasPermission('create competitions');
    }

    public function update(User $user, Competition $competition)
    {
        return $user->hasPermission('edit competitions');
    }

    public function delete(User $user, Competition $competition)
    {
        return $user->hasPermission('delete competitions');
    }

    public function managePrizes(User $user)
    {
        return $user->hasPermission('manage competition prizes');
    }

    public function manageParticipants(User $user)
    {
        return $user->hasPermission('manage competition participants');
    }
}
