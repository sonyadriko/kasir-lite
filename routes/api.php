<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CashSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Create token with different expiry based on remember me
    $tokenName = 'pos-token-' . now()->timestamp;
    $token = $user->createToken($tokenName)->plainTextToken;
    
    // Set token expiry in response for frontend to handle
    $expiryHours = $request->remember ? 24 * 7 : 24; // 7 days or 1 day
    
    return response()->json([
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer',
        'expires_in_hours' => $expiryHours,
        'message' => 'Login berhasil'
    ]);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Development routes (remove in production)
if (app()->environment(['local', 'development']) || 
    in_array(request()->getHost(), ['127.0.0.1', 'localhost'])) {
    Route::prefix('dev')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/sales', [SalesController::class, 'store']);
    });
}

// Protected API routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    
    // Sales
    Route::post('/sales', [SalesController::class, 'store']);
    
    // Cash Sessions
    Route::post('/cash-sessions/open', [CashSessionController::class, 'open']);
    Route::post('/cash-sessions/close', [CashSessionController::class, 'close']);
    Route::get('/cash-sessions/active', [CashSessionController::class, 'active']);
});
