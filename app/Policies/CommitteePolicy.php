<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Committee;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommitteePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
            return true;
        }
    }

    public function view(User $user, Committee $committee)
    {
        return $user->hasPermission('view committees', $committee);
    }

    public function create(User $user)
    {
        return $user->hasPermission('create committees');
    }

    public function update(User $user, Committee $committee)
    {
        return $user->hasPermission('edit committees', $committee);
    }

    public function delete(User $user, Committee $committee)
    {
        return $user->hasPermission('delete committees', $committee);
    }
}
