<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'slug',
        'overview',
        'description',
        'logo',
        'cover_image',
        'start_date',
        'end_date',
        'location',
        'status',
    ];

    // Cast timestamps and date fields
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}
