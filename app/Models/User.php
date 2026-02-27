<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'avatar_src',
        'linkedin',
        'email_verified_at',
        'remember_token',
        'groupable_id',
        'groupable_type',
        'phone_number',
        'national_id'
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
            ->withPivot(['scopeable_type', 'scopeable_id', 'is_manual'])
            ->withTimestamps();
    }

    /**
     * Assign a role to the user in a specific scope.
     *
     * @param  \App\Models\Role|string  $role
     * @param  \Illuminate\Database\Eloquent\Model|null  $scope
     * @return \App\Models\UserRole
     */
    public function assignRole($role, $scope = null, bool $manual = false)
    {
        $role = $role instanceof Role ? $role : Role::where('name', $role)->firstOrFail();

        $this->roles()->attach($role->id, [
            'scopeable_type' => $scope ? $scope->getMorphClass() : null,
            'scopeable_id' => $scope ? $scope->id : null,
            'is_manual' => $manual,
        ]);

        // Return the UserRole instance
        return $this->roleAssignments()
            ->where('role_id', $role->id)
            ->where('scopeable_type', $scope ? $scope->getMorphClass() : null)
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
                return $query->wherePivot('scopeable_type', $scope->getMorphClass())
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
                return $query->where('scopeable_type', $scope->getMorphClass())
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
                return $query->where('scopeable_type', $scope->getMorphClass())
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
            ->where(function ($query) use ($scope) {
                // Always check global roles (they grant permission everywhere)
                $query->where(function ($q) {
                    $q->whereNull('scopeable_type')->whereNull('scopeable_id');
                });

                // Also check scoped roles if a scope is provided
                if ($scope) {
                    $query->orWhere(function ($q) use ($scope) {
                        $q->where('scopeable_type', $scope->getMorphClass())
                            ->where('scopeable_id', $scope->id);
                    });
                }
            })
            ->exists();
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class);
    }

    public function assignDefaultRole()
    {
        // Remove only auto-assigned roles (preserve manual ones)
        $this->roleAssignments()->where('is_manual', false)->delete();

        // IEEE chapter members get ieee admin (global scope)
        if (
            $this->groupable_type === 'chapter' &&
            $this->groupable &&
            $this->groupable->short_name === 'IEEE'
        ) {
            $roleModel = Role::where('name', 'ieee admin')->first();
            if ($roleModel) {
                $this->roles()->syncWithoutDetaching([
                    $roleModel->id => [
                        'scopeable_type' => null,
                        'scopeable_id' => null,
                        'is_manual' => false,
                    ],
                ]);
            }
            return;
        }

        // Load positions with their linked roles
        $positions = $this->positions()->with('role')->get();

        // No positions → assign member role (e.g. new registered users)
        if ($positions->isEmpty()) {
            $memberRole = Role::where('name', 'member')->first();
            if ($memberRole) {
                $this->roles()->syncWithoutDetaching([
                    $memberRole->id => [
                        'scopeable_type' => null,
                        'scopeable_id' => null,
                        'is_manual' => false,
                    ],
                ]);
            }
            return;
        }

        foreach ($positions as $position) {
            $roleModel = null;
            $scope = null;

            // If position has a linked role, use it directly
            if ($position->role_id) {
                $roleModel = $position->role;
                // Determine scope based on role's scope_type
                $scope = match ($roleModel->scope_type) {
                    'chapter', 'committee' => $this->groupable,
                    'track' => $this->track,
                    default => null,
                };
            } else {
                // Fallback to hardcoded mapping for seeded positions
                $positionName = strtolower($position->name);

                $roleName = match (true) {
                    $positionName === 'chairperson' && $this->groupable_type === 'chapter'
                        => 'chapter chairperson',
                    in_array($positionName, ['vice chairperson', 'technical vice chairperson', 'managerial vice chairperson']) && $this->groupable_type === 'chapter'
                        => 'vice chapter chairperson',
                    $positionName === 'lead' && $this->groupable_type === 'committee'
                        => 'committee leader',
                    $positionName === 'lead' && $this->groupable_type === 'chapter'
                        => 'chapter chairperson',
                    $positionName === 'vice lead' && $this->groupable_type === 'committee'
                        => 'vice committee leader',
                    $positionName === 'vice lead' && $this->groupable_type === 'chapter'
                        => 'vice chapter chairperson',
                    $positionName === 'track lead'
                        => 'track leader',
                    $positionName === 'track vice-lead'
                        => 'vice track leader',
                    default => 'member',
                };

                $roleModel = Role::where('name', $roleName)->first();

                $scope = match ($roleName) {
                    'track leader', 'vice track leader' => $this->track,
                    'member' => null,
                    default => $this->groupable,
                };
            }

            if (!$roleModel) {
                continue;
            }

            $this->roles()->syncWithoutDetaching([
                $roleModel->id => [
                    'scopeable_type' => $scope?->getMorphClass(),
                    'scopeable_id' => $scope?->id,
                    'is_manual' => false,
                ],
            ]);
        }
    }


    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participants')
                    ->withPivot('role')
                    ->withTimestamps();
    }

}
