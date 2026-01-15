<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $fillable = [
        'summary_text',
        'season_id'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
