<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Chapter;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChapterPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        // أي يوزر في IEEE Chapter ياخد كل الصلاحيات
        if ($user->groupable_type === 'chapter' && $user->groupable->short_name === 'IEEE') {
            return true;
        }
    }

    public function view(User $user, Chapter $chapter)
    {
        return $user->hasPermission('view chapters', $chapter);
    }

    public function create(User $user)
    {
        return $user->hasPermission('create chapters');
    }

    public function update(User $user, Chapter $chapter)
    {
        return $user->hasPermission('edit chapters', $chapter);
    }

    public function delete(User $user, Chapter $chapter)
    {
        return $user->hasPermission('delete chapters', $chapter);
    }
}
