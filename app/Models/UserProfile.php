<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'no_telp',
        'no_wa',
        'jabatan',
        'alamat_kantor',
        'nama_bank',
        'no_rekening',
        'ukuran_baju',
        'institusi_id',
        'is_smoking',
        'avatar'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institusi()
    {
        return $this->belongsTo(Institusi::class);
    }
}
