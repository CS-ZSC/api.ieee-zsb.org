<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'track_id',
        'position',
        'avatar_src',
        'linkedin',
        'email_verified_at',
        'remember_token',
        'groupable_id',
        'groupable_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function groupable()
    {
        return $this->morphTo();
    }

    /**
     * Get all role assignments for the user.
     */
    public function roleAssignments(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Get all roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->using(UserRole::class)
            ->withPivot(['scopeable_type', 'scopeable_id'])
            ->withTimestamps();
    }

    /**
     * Assign a role to the user in a specific scope.
     *
     * @param  \App\Models\Role|string  $role
     * @param  \Illuminate\Database\Eloquent\Model|null  $scope
     * @return \App\Models\UserRole
     */
    public function assignRole($role, $scope = null)
    {
        $role = $role instanceof Role ? $role : Role::where('name', $role)->firstOrFail();

        $this->roles()->attach($role->id, [
            'scopeable_type' => $scope ? get_class($scope) : null,
            'scopeable_id' => $scope ? $scope->id : null,
        ]);

        // Return the UserRole instance
        return $this->roleAssignments()
            ->where('role_id', $role->id)
            ->where('scopeable_type', $scope ? get_class($scope) : null)
            ->where('scopeable_id', $scope ? $scope->id : null)
            ->first();
    }

    /**
     * Remove a role from the user in a specific scope.
     *
     * @param  \App\Models\Role|string  $role
     * @param  \Illuminate\Database\Eloquent\Model|null  $scope
     * @return int
     */
    public function removeRole($role, $scope = null)
    {
        $role = $role instanceof Role ? $role : Role::where('name', $role)->firstOrFail();

        return $this->roles()
            ->wherePivot('role_id', $role->id)
            ->when($scope, function ($query) use ($scope) {
                return $query->wherePivot('scopeable_type', get_class($scope))
                    ->wherePivot('scopeable_id', $scope->id);
            }, function ($query) {
                return $query->whereNull('scopeable_type')
                    ->whereNull('scopeable_id');
            })
            ->detach();
    }

    /**
     * Check if the user has a specific role in any scope.
     *
     * @param  string  $roleName
     * @param  \Illuminate\Database\Eloquent\Model|null  $scope
     * @return bool
     */
    public function hasRole(string $roleName, $scope = null): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->when($scope, function ($query) use ($scope) {
                return $query->where('scopeable_type', get_class($scope))
                    ->where('scopeable_id', $scope->id);
            }, function ($query) {
                return $query->whereNull('scopeable_type')
                    ->whereNull('scopeable_id');
            })
            ->exists();
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param  array  $roles
     * @param  \Illuminate\Database\Eloquent\Model|null  $scope
     * @return bool
     */
    public function hasAnyRole(array $roles, $scope = null): bool
    {
        return $this->roles()
            ->whereIn('name', $roles)
            ->when($scope, function ($query) use ($scope) {
                return $query->where('scopeable_type', get_class($scope))
                    ->where('scopeable_id', $scope->id);
            }, function ($query) {
                return $query->whereNull('scopeable_type')
                    ->whereNull('scopeable_id');
            })
            ->exists();
    }

    /**
     * Check if the user has a specific permission in any scope.
     *
     * @param  string  $permissionName
     * @param  \Illuminate\Database\Eloquent\Model|null  $scope
     * @return bool
     */
    public function hasPermission(string $permissionName, $scope = null): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->when($scope, function ($query) use ($scope) {
                return $query->where('scopeable_type', get_class($scope))
                    ->where('scopeable_id', $scope->id);
            }, function ($query) {
                return $query->whereNull('scopeable_type')
                    ->whereNull('scopeable_id');
            })
            ->exists();
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function assignDefaultRole()
    {
        $role = null;
        $scope = null;

        if (
            $this->groupable_type === 'App\\Models\\Chapter' &&
            $this->groupable &&
            $this->groupable->short_name === 'IEEE'
        ) {
            // IEEE Admin - global scope
            $role = 'ieee admin';
            $scope = null;
        } else {
            // For all other roles
            $position = strtolower($this->position);
            switch ($position) {
                case 'chapter chairperson':
                case 'vice chapter chairperson':
                case 'committee leader':
                case 'vice committee leader':
                    $role = $position;
                    $scope = $this->groupable;
                    break;
                case 'track leader':
                case 'vice track leader':
                    $role = $position;
                    $scope = $this->track;
                    break;
                default:
                    $role = 'member';
                    $scope = $this->groupable;
            }
        }

        // Remove any existing roles
        $this->roles()->detach();

        // Find the role
        $roleModel = Role::where('name', $role)->first();
        if (!$roleModel) {
            return; // Role not found
        }

        // Prepare the pivot data
        $pivotData = [
            'scopeable_type' => $scope ? get_class($scope) : null,
            'scopeable_id' => $scope ? $scope->id : null,
        ];

        // Attach the role with scope
        $this->roles()->attach($roleModel->id, $pivotData);
    }
}
