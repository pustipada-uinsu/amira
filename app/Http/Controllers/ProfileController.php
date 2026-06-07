<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\UserProfile;
use App\Models\Institusi;

class ProfileController extends Controller
{

    public function create()
    {
        $institusi = Institusi::orderBy('nama_zona')
            ->orderBy('nama_institusi')
            ->get()
            ->groupBy('nama_zona');
        return inertia('Profile/Complete', [
            'user' => Auth::user(),

            'profile' => [
                ...(Auth::user()->profile?->toArray() ?? []),

                'avatar_url' => Auth::user()->profile?->avatar
                    ? Storage::url(Auth::user()->profile->avatar)
                    : null,
            ],

            'institusi' => $institusi,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'alamat'         => 'required|string',
            'no_telp'        => 'required|string|max:20',
            'no_wa'          => 'required|string|max:20',
            'jabatan'        => 'required|string|max:255',
            'alamat_kantor'  => 'required|string',
            'nama_bank'      => 'nullable|string|max:64',
            'no_rekening'    => 'nullable|string|max:32',
            'ukuran_baju'    => 'required|in:M,L,XL,XXL,XXXL',
            'institusi_id'   => 'required|exists:institusi,id',
            'is_smoking'     => 'boolean',
            'avatar'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        $profile = $user->profile;

        $user->update([
            'name' => $request->nama_lengkap
        ]);

        $avatarPath = $profile?->avatar;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();

            $avatarPath = $file->storeAs('avatars', $filename, 'public');

            if ($profile && $profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
        }

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap'  => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat'        => $request->alamat,
                'no_telp'       => $request->no_telp,
                'no_wa'         => $request->no_wa,
                'jabatan'       => $request->jabatan,
                'alamat_kantor' => $request->alamat_kantor,
                'nama_bank'     => $request->nama_bank,
                'no_rekening'   => $request->no_rekening,
                'ukuran_baju'   => $request->ukuran_baju,
                'is_smoking'    => $request->is_smoking ?? false,
                'institusi_id'  => $request->institusi_id,
                'avatar'        => $avatarPath,
            ]
        );

        return redirect()->route('dashboard')
            ->with('success', 'Profil berhasil disimpan');
    }

    public function edit()
    {
        $institusi = Institusi::orderBy('nama_zona')
            ->orderBy('nama_institusi')
            ->get()
            ->groupBy('nama_zona');

        return inertia('Profile/Complete', [
            'user' => Auth::user(),
            'profile' => Auth::user()->profile,
            'institusi' => $institusi,
        ]);
    }
}
