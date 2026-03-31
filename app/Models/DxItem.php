<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DxItem extends Model
{
    protected $table = 'dx_items';

    protected $fillable = [
        'category',
        'slug',
        'parent_id',
        'domain_key',
        'code',
        'icon',
        'image_path',
        'file_url',
        'sort_order',
        'is_active',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('title');
    }
}
