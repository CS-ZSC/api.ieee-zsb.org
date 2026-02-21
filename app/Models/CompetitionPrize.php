<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionPrize extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'title',
        'rank',
        'prize_description',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
