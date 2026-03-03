<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'event_participant_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function eventParticipant()
    {
        return $this->belongsTo(EventParticipant::class);
    }
}
