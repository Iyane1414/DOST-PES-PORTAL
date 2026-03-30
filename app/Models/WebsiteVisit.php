<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisit extends Model
{
    protected $fillable = [
        'page',
        'session_id',
        'ip_address',
        'user_agent',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }
}
