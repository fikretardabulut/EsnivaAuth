<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('auth-login', function (Request $request) {
            $email = (string) $request->input('email');
            $ip = (string) $request->ip();

            return Limit::perMinute(5)->by($email . '|' . $ip)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again later.',
                    'errors' => null,
                ], 429);
            });
        });

        RateLimiter::for('auth-api', function (Request $request) {
            $key = $request->user()?->id
                ? 'user:' . $request->user()->id
                : 'ip:' . $request->ip();

            return Limit::perMinute(60)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                    'errors' => null,
                ], 429);
            });
        });

        Log::info('Esniva Auth security rate limiters initialized.');
    }
}