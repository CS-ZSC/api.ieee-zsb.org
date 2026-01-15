<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'logo',
        'color_scheme_1',
        'color_scheme_2',
    ];

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function users()
    {
        return $this->morphMany(User::class, 'groupable');
    }

    public function descriptions()
    {
        return $this->hasMany(Description::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
