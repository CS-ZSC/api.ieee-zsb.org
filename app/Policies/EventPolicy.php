<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
            return true;
        }
    }

    public function view(User $user, Event $event)
    {
        return $user->hasPermission('view events');
    }

    public function create(User $user)
    {
        return $user->hasPermission('create events');
    }

    public function update(User $user, Event $event)
    {
        return $user->hasPermission('edit events');
    }

    public function delete(User $user, Event $event)
    {
        return $user->hasPermission('delete events');
    }

    public function manageImages(User $user)
    {
        return $user->hasPermission('manage event images');
    }

    public function manageCompetitions(User $user)
    {
        return $user->hasPermission('manage competitions');
    }

    public function manageSpeakers(User $user)
    {
        return $user->hasPermission('manage speakers');
    }

    public function manageSponsors(User $user)
    {
        return $user->hasPermission('manage sponsors');
    }

    public function manageParticipants(User $user)
    {
        return $user->hasPermission('manage event participants');
    }

    public function manageTickets(User $user)
    {
        return $user->hasPermission('manage tickets');
    }
}
