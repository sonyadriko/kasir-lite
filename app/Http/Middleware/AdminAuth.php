<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has token in localStorage (client-side auth)
        // For demo purposes, we'll check session or token via JavaScript
        
        // If this is an AJAX request, check for Bearer token
        if ($request->ajax() || $request->wantsJson()) {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }
        
        // For regular web requests, we'll rely on JavaScript to check localStorage
        // and redirect if needed. The layout template will handle this.
        
        return $next($request);
    }
}