<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserProfileCompleted
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'user') {
            return $next($request);
        }

        $profile = $user->profile;

        if (!$profile) {
            return redirect()->route('profile.complete');
        }

        $isIncomplete =
            !$profile->nama_lengkap ||
            !$profile->jenis_kelamin ||
            !$profile->alamat ||
            !$profile->no_telp ||
            !$profile->no_wa;

        if ($isIncomplete) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}
