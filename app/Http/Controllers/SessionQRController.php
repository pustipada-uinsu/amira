<?php

namespace App\Http\Controllers;

use App\Models\EventSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SessionQRController extends Controller
{
    public function generate(EventSession $session)
    {
        if (!auth()->user()->canAccessBy($session->event)) {
            abort(403);
        }

        $code = Str::random(10);

        $key = "session_qr_{$session->id}";

        Cache::put($key, [
            'code' => $code,
            'expired_at' => now()->addSeconds(5)
        ], now()->addSeconds(5));

        return response()->json([
            'code' => $code
        ]);
    }
}