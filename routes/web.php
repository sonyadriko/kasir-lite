<?php

use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Login page
Route::get('/login', function () {
    return view('login');
});

// POS Interface
Route::get('/pos', function () {
    return view('pos', [
        'outlet' => (object) ['name' => 'Toko Cibubur'],
        'user' => (object) ['name' => 'Cashier Demo']
    ]);
});

// Receipt routes (demo - remove auth for easy testing)
Route::get('/receipt/{sale}', [ReceiptController::class, 'show'])->name('receipt.show');

// Demo receipt route for development
Route::get('/demo-receipt', function () {
    // Create a sample sale for demo receipt
    $outlet = \App\Models\Outlet::first();
    $cashier = \App\Models\User::where('role', 'cashier')->first();
    
    if (!$outlet || !$cashier) {
        return 'Demo data not found. Please run: php artisan migrate:fresh --seed';
    }
    
    $demoSale = (object) [
        'id' => 999999,
        'invoice_no' => 'DEMO/' . date('Ym') . '/0001',
        'outlet' => $outlet,
        'cashier' => $cashier,
        'sold_at' => now(),
        'items' => collect([
            (object) [
                'name_snapshot' => 'Demo Product 1',
                'qty' => 2,
                'price' => 10000,
                'discount_amount' => 1000,
                'total' => 19000
            ],
            (object) [
                'name_snapshot' => 'Demo Product 2',
                'qty' => 1,
                'price' => 15000,
                'discount_amount' => 0,
                'total' => 15000
            ]
        ]),
        'payments' => collect([
            (object) [
                'method' => 'CASH',
                'amount' => 50000
            ]
        ]),
        'subtotal' => 34000,
        'discount_amount' => 2000,
        'tax_amount' => 3200,
        'rounding' => 0,
        'total' => 35200,
        'paid' => 50000,
        'change_amount' => 14800,
        'note' => 'Demo transaction for testing'
    ];
    
    return view('receipt', ['sale' => $demoSale]);
});
