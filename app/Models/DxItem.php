<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DxItem extends Model
{
    protected $table = 'dx_items';

    protected $fillable = [
        'category',
        'title',
        'description',
    ];
}
