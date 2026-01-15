<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'name',
        'hashtag',
        'description',
        'image',
    ];

    public function users()
    {
        return $this->morphMany(User::class, 'groupable');
    }

    public function goals()
    {
        return $this->morphMany(Goal::class, 'goalable');
    }
}
