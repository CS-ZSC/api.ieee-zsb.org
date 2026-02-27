<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSpeaker extends Model
{
    //
    protected $table = 'event_speakers';
    protected $fillable = [
        'event_id',
        'name',
        'email',
        'linkedin_url',
        'bio',
        'photo'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
