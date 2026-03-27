<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
