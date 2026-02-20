<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Chapter;
use App\Models\Committee;
use App\Models\Track;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property string|null $scopeable_type
 * @property int|null $scopeable_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read User $user
 * @property-read Role $role
 * @property-read Model|Chapter|Committee|Track|null $scopeable
 */
class UserRole extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'role_id',
        'scopeable_type',
        'scopeable_id',
        'is_manual',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_manual' => 'boolean',
    ];

    /**
     * Get the user that owns the role assignment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, UserRole>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that is assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Role, UserRole>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the parent scopeable model (Chapter, Committee, Track, or null for global).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, UserRole>
     */
    public function scopeable(): MorphTo
    {
        return $this->morphTo();
    }
}
