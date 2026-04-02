<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $category
 * @property \Illuminate\Support\Carbon $date
 * @property string $division
 * @property string $url
 */
class Issuance extends Model
{
    protected $fillable = [
        'title',
        'category',
        'date',
        'division',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
