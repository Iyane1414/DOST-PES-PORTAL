<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $eyebrow
 * @property string $title
 * @property \Illuminate\Support\Carbon $date
 * @property string $summary
 * @property string $content
 * @property string|null $link_url
 * @property string|null $thumbnail_path
 * @property string $accent
 * @property string|null $image_alt
 */
class News extends Model
{
    protected $fillable = [
        'eyebrow',
        'title',
        'date',
        'summary',
        'content',
        'link_url',
        'thumbnail_path',
        'accent',
        'image_alt',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
