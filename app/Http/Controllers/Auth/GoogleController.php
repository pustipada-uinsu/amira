<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'username'  => $googleUser->getEmail(),
                'password'  => bcrypt(uniqid()),
                'role'   => 'user',
                'google_id' => $googleUser->getId(),
            ]);
        }

        if (!$user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('post.login.redirect');
    }

    // public function handleGoogleMobile(Request $request)
    // {
    //     $accessToken = $request->query('access_token');

    //     if (!$accessToken) {
    //         return redirect()->route('login')->with('error', 'Access Token tidak ditemukan.');
    //     }

    //     // Validasi token ke Google
    //     $response = Http::get('https://www.googleapis.com/oauth2/v1/tokeninfo', [
    //         'access_token' => $accessToken,
    //     ]);

    //     if ($response->failed()) {
    //         // return redirect()->route('login')->with('error', 'Access Token tidak valid atau telah kedaluwarsa.');
    //         return redirect()->to('https://my.uinsu.ac.id/login?error=Access+Token+tidak+valid+atau+telah+kedaluwarsa');
    //     }

    //     $payload = $response->json();
    //     $clientId = env('GOOGLE_CLIENT_ANDROID_ID');
    //     if (($payload['audience'] ?? null) !== $clientId &&
    //         ($payload['issued_to'] ?? null) !== $clientId) {
    //         return redirect()->route('login')->with('error', 'Token tidak cocok dengan aplikasi yang sah.');
    //     }

    //     $email = $payload['email'] ?? null;
    //     if (!$email) {
    //         return redirect()->route('login')->with('error', 'Email tidak ditemukan dalam token Google.');
    //     }

    //     $user = User::where('email', $email)->first();
    //     if (!$user) {
    //         // return redirect()->route('login')->with('error', 'Akun Anda tidak terdaftar pada sistem SSO.');
    //         return redirect()->to('https://my.uinsu.ac.id/login?error=Akun+Anda+tidak+terdaftar+pada+sistem+SSO');
    //     }

    //     // Login user
    //     Auth::login($user);

    //     // return redirect()->to('/dashboard-from-mobile');

    //     $signedUrl = URL::signedRoute('mobile.dashboard', ['userId' => $user->id], now()->addMinutes(2));
    //     // return redirect('/dashboard-from-mobile');
    //     return redirect($signedUrl);
    // }
    public function handleGoogleMobile(Request $request)
    {
        $accessToken = $request->query('access_token');

        if (!$accessToken) {
            return $this->mobileError('Access Token tidak ditemukan.');
        }

        $response = Http::get(
            'https://www.googleapis.com/oauth2/v1/tokeninfo',
            ['access_token' => $accessToken]
        );

        if ($response->failed()) {
            return $this->mobileError(
                'Access Token tidak valid atau telah kedaluwarsa'
            );
        }

        $payload = $response->json();
        $clientId = env('GOOGLE_CLIENT_ANDROID_ID');

        if (
            ($payload['audience'] ?? null) !== $clientId &&
            ($payload['issued_to'] ?? null) !== $clientId
        ) {
            return $this->mobileError('Token tidak cocok dengan aplikasi yang sah.');
        }

        $email = $payload['email'] ?? null;
        if (!$email) {
            return $this->mobileError('Email tidak ditemukan dalam token Google.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return $this->mobileError(
                'Akun Anda tidak terdaftar pada sistem SSO'
            );
        }

        Auth::login($user);

        $signedUrl = URL::signedRoute(
            'mobile.dashboard',
            ['userId' => $user->id],
            now()->addMinutes(2)
        );

        return redirect($signedUrl);
    }

    private function mobileError(string $message)
    {
        return redirect()->to(
            'https://my.uinsu.ac.id/login?error=' . urlencode($message)
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            return back()->with('error', 'Username atau password salah');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
