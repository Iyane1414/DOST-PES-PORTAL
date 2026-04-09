<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatesProject extends Model
{
    protected $fillable = [
        'title',
        'code',
        'type',
        'news_eyebrow',
        'description',
        'news_summary',
        'news_content',
        'date',
        'url',
        'news_link_url',
        'news_accent',
        'news_image_alt',
        'thumbnail_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
