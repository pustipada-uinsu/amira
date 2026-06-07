<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use App\Services\DiscordService;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'login-direct',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'profile.complete' => \App\Http\Middleware\EnsureUserProfileCompleted::class,
            'active' => \App\Http\Middleware\CheckUserActive::class,
            'log' => \App\Http\Middleware\LogActivity::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        $exceptions->report(function (Throwable $e) {
            $user = request()?->user()?->username ?? 'No Auth';
            try {
                DiscordService::send(
                    "🚨 **Error Detected**\n" .
                        "**User:** {$user}\n" .
                        "**Message:** {$e->getMessage()}\n" .
                        "**File:** {$e->getFile()}:{$e->getLine()}"
                );
            } catch (Throwable $err) {
                Log::error('Gagal mengirim notifikasi Discord: ' . $err->getMessage());
            }
        });
    })->create();
