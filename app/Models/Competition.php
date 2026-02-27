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

    public function prizes()
    {
        return $this->hasMany(CompetitionPrize::class);
    }
}
