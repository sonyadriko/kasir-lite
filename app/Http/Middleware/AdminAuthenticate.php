<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is already authenticated via session
        if (auth()->check()) {
            return $next($request);
        }
        
        // Check for token-based authentication (from localStorage)
        $token = $request->bearerToken() ?? $request->header('X-Auth-Token');
        
        // If no token in headers, check for admin session via localStorage simulation
        if (!$token && $request->hasSession()) {
            $token = $request->session()->get('admin_token');
        }
        
        // For admin routes accessed directly, check if user data exists in session
        if (!$token && $request->is('admin/*')) {
            // For development/demo purposes, auto-login the first user if no auth
            if (app()->environment(['local', 'development'])) {
                $user = User::first();
                if ($user) {
                    auth()->login($user);
                    return $next($request);
                }
            }
        }
        
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken && $accessToken->tokenable) {
                auth()->login($accessToken->tokenable);
                return $next($request);
            }
        }
        
        // If this is an API request, return JSON error
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // For web requests, redirect to login
        return redirect()->route('login')->with('error', 'Please login to access this page.');
    }
}