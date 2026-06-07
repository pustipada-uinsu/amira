<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'banner',
        'flyer',
        'public',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'venue_id',
        'custom_location',
        'created_by',
        'status',
    ];

    protected $casts = [
        'public' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sessions()
    {
        return $this->hasMany(EventSession::class);
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'event_admins')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getRouteKey()
    {
        return Hashids::encode($this->id);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);

        if (empty($decoded)) {
            abort(404);
        }

        return $this->where('id', $decoded[0])->firstOrFail();
    }


    protected $appends = ['hashid'];

    public function getHashidAttribute()
    {
        return Hashids::encode($this->id);
    }

    public function canAccessBy($user)
    {
        return $user->role === 'adminsuper'
            || $this->created_by === $user->id
            || $this->admins()->where('user_id', $user->id)->exists();
    }

    public function canEditBy($user)
    {
        return $user->role === 'adminsuper'
            || $this->created_by === $user->id
            || $this->admins()->where('user_id', $user->id)->exists();
    }

    public function canDeleteBy($user)
    {
        return $user->role === 'adminsuper'
            || $this->created_by === $user->id;
    }
}
