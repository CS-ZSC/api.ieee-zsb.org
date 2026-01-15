<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'name',
        'hashtag',
        'image',
        'description',
        'chapter_id'
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function goals()
    {
        return $this->morphMany(Goal::class, 'goalable');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'track_id');
    }
}
