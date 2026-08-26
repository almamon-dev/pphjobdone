<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
        'service_id',
        'title',
        'lead_name',
        'company_name',
        'website_url',
        'goals',
        'budget',
        'proposal_data',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'proposal_data' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
