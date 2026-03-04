<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'leader_event_participant_id',
        'name',
        'join_code',
    ];

    public static function generateUniqueJoinCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function leaderEventParticipant()
    {
        return $this->belongsTo(EventParticipant::class, 'leader_event_participant_id');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }
}
