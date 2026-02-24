<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSponsor extends Model
{
    //
    protected $table = 'event_sponsors';

    protected $fillable = [
        'event_id',
        'name',
        'logo',
        'website_url'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
