<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $category
 * @property string $slug
 * @property int|null $parent_id
 * @property string $domain_key
 * @property string|null $code
 * @property string|null $icon
 * @property string|null $image_path
 * @property string|null $file_url
 * @property int $sort_order
 * @property bool $is_active
 * @property string $title
 * @property string $description
 */
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
