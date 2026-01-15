<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'goal',
        'goalable_id',
        'goalable_type'
    ];

    public function goalable()
    {
        return $this->morphTo();
    }
}
