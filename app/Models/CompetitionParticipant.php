<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'event_participant_id',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function eventParticipant()
    {
        return $this->belongsTo(EventParticipant::class);
    }
}
