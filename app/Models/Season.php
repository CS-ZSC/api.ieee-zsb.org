<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'year',
        'chapter_id'
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function summaries()
    {
        return $this->hasMany(Summary::class);
    }
}
