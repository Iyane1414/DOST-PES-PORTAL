<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
