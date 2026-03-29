<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'booking_id',
        'title',
        'description',
        'progress',
        'status',
        'due_date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
