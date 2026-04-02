<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property \Illuminate\Support\Carbon $date
 * @property string $division
 * @property string $url
 */
class Material extends Model
{
    protected $fillable = [
        'title',
        'type',
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
