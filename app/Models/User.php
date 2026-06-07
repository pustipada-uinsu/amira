<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'google_id',
        'is_active',
        'pin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function managedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_admins')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function sessionAttendances()
    {
        return $this->hasMany(SessionAttendance::class);
    }
}
