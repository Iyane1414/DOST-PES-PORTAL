<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'opened_at',
        'replied_at',
        'admin_reply_subject',
        'admin_reply_body',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'replied_at' => 'datetime',
    ];
}
