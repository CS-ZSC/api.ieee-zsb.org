<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    protected $fillable = [
        'about',
        'vision',
        'mission',
        'chapter_id'
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
