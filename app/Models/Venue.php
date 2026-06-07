<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'maps_url',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
