<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'start_at',
        'end_at',
        'session_date',
        'description',
        'location',
        'materials',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'session_date' => 'date',
        'materials' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendances()
    {
        return $this->hasMany(SessionAttendance::class);
    }
}
