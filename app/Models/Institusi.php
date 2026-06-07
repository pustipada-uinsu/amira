<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institusi extends Model
{
    protected $table = 'institusi';
    protected $fillable = [
        'id_institusi',
        'nama_institusi',
        'id_zona',
        'nama_zona',
    ];

    public function profiles()
    {
        return $this->hasMany(UserProfile::class);
    }
}


