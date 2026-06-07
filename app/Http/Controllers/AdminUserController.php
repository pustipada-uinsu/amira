<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('profile');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%$request->search%")
                  ->orWhere('email', 'like', "%$request->search%");
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function toggleSuspend(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'Status user diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User dihapus');
    }

    public function update(Request $request, User $user)
    { 
        // return $request->all();
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->withErrors([
                'pin' => 'PIN tidak valid'
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required',

            'nama_lengkap' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'no_wa' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'alamat_kantor' => 'nullable|string',
            'nama_bank' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'ukuran_baju' => 'nullable|string',
            'is_smoking' => 'nullable|boolean',
            'institusi_id' => 'nullable|exists:institusi,id',
        ]);

        $old = $user->toArray();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $request->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'no_wa' => $request->no_wa,
                'jabatan' => $request->jabatan,
                'alamat_kantor' => $request->alamat_kantor,
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'ukuran_baju' => $request->ukuran_baju,
                'is_smoking' => $request->is_smoking,
                'institusi_id' => $request->institusi_id,
            ]
        );

        Log::info('ADMIN UPDATE USER', [
            'actor' => Auth::user()->email,
            'target' => $user->email,
            'old' => $old,
            'new' => $user->fresh()->toArray(),
        ]);

        return back()->with('success', 'User berhasil diupdate');
    }
}