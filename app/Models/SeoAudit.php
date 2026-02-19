<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoAudit extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'url',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];
}
