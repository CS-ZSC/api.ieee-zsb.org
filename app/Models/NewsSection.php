<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsSection extends Model
{
    protected $fillable = [
        'news_id',
        'heading',
        'descriptions',
        'photo',
        'photo_description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'descriptions' => 'array',
        ];
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
