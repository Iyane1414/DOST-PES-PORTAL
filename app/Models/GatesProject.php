<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatesProject extends Model
{
    protected $fillable = [
        'title',
        'code',
        'type',
        'description',
        'date',
        'url',
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
