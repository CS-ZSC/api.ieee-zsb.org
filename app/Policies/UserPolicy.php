<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
            return true;
        }
    }

    public function viewAny(User $user)
{
    return $user->hasPermission('view users');
}


    public function view(User $user, User $model)
    {
        return $user->hasPermission('view users', $model->groupable);
    }

    public function create(User $user)
    {
        return $user->hasPermission('create users');
    }

    public function update(User $user, User $model)
    {
        return $user->hasPermission('edit users', $model->groupable);
    }

    public function delete(User $user, User $model)
    {
        return $user->hasPermission('delete users', $model->groupable);
    }
}
