<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $logo
 * @property string $color_scheme_1
 * @property string $color_scheme_2
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read Collection<int, Track> $tracks
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Description> $descriptions
 * @property-read Collection<int, Season> $seasons
 * @property-read Collection<int, UserRole> $roleAssignments
 * @property-read Collection<int, Role> $roles
 */
class Chapter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'logo',
        'color_scheme_1',
        'color_scheme_2',
    ];

    /**
     * Get all tracks for the chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Track>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    /**
     * Get all users associated with the chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<User>
     */
    public function users(): MorphMany
    {
        return $this->morphMany(User::class, 'groupable');
    }

    /**
     * Get all descriptions for the chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Description>
     */
    public function descriptions(): HasMany
    {
        return $this->hasMany(Description::class);
    }

    /**
     * Get all seasons for the chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Season>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Get all role assignments for this chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<UserRole>
     */
    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(UserRole::class, 'scopeable');
    }

    /**
     * Get all roles assigned within this chapter's scope.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<Role>
     */
    public function roles()
    {
        return $this->hasManyThrough(
            Role::class,
            UserRole::class,
            'scopeable_id',
            'id',
            'id',
            'role_id'
        )->where('scopeable_type', self::class);
    }

    /**
     * Get all users with a specific role in this chapter.
     *
     * @param string $roleName
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function getUsersWithRole(string $roleName)
    {
        return User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName)
                ->where('scopeable_type', self::class)
                ->where('scopeable_id', $this->id);
        })->get();
    }
}
