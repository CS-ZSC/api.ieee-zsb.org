<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'chapter_id',
        'name',
        'overview',
        'type',
        'max_team_members',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function prizes()
    {
        return $this->hasMany(CompetitionPrize::class);
    }

    public function participants()
    {
        return $this->hasMany(CompetitionParticipant::class);
    }
}
