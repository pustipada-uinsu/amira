<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionAttendance extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'attendance_date',
        'checked_in_at',
        'method',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        // 'checked_out_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}