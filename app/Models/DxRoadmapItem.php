<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DxRoadmapItem extends Model
{
    protected $fillable = [
        'year_label',
        'title',
        'description',
        'milestones',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'milestones' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
