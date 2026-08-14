<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'email',
        'phone',
        'company_name',
        'service_interest',
        'budget',
        'qualification_status',
        'qualification_summary',
        'chat_history',
        'status',
    ];

    protected $casts = [
        'chat_history' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
