<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        // return request()->headers->all();

        return Inertia::render('Auth/LoginForm', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function login_admin(): Response
    {
        return Inertia::render('Auth/LoginAdmin', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($request->session()->has('oauth_redirect_uri')) {

            $code = \Illuminate\Support\Str::random(40);

            \Illuminate\Support\Facades\Cache::put('oauth_code_' . $code, $user->id, now()->addMinutes(5));

            $redirectUri = $request->session()->pull('oauth_redirect_uri');
            $state = $request->session()->pull('oauth_state');

            return redirect()->away("$redirectUri?code=$code&state=$state");
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
