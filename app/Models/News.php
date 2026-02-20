<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date_created',
        'author',
        'home_item',
        'tags',
        'main_photo',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'home_item' => 'boolean',
            'date_created' => 'date',
        ];
    }

    public function sections()
    {
        return $this->hasMany(NewsSection::class)->orderBy('sort_order');
    }
}
