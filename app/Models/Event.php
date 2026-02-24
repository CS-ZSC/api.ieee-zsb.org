<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the images for the event.
     */
    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class);
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(EventSponsor::class);
    }
}
