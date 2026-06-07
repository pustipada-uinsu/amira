<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Client;
use Vinkla\Hashids\Facades\Hashids;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Route::bind('client', function ($value) {
            $id = Hashids::decode($value)[0] ?? null;
            return Client::findOrFail($id);
        });
    }
}
