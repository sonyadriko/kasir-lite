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
})->name('login');

// Admin login redirect - for demo purposes
Route::get('/admin/login', function() {
    return redirect('/login');
});

// Admin authentication session handler
Route::post('/admin/authenticate', function(\Illuminate\Http\Request $request) {
    // Validate token from localStorage
    $token = $request->input('token');
    
    if (!$token) {
        return response()->json(['error' => 'No token provided'], 401);
    }
    
    // Find the token in the database
    $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
    
    if (!$accessToken || !$accessToken->tokenable) {
        return response()->json(['error' => 'Invalid token'], 401);
    }
    
    // Log the user in for the session
    auth()->login($accessToken->tokenable);
    $request->session()->put('admin_token', $token);
    
    return response()->json([
        'success' => true, 
        'user' => $accessToken->tokenable,
        'message' => 'Authenticated successfully'
    ]);
})->name('admin.authenticate');

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

// Admin routes - client-side authentication via localStorage
Route::prefix('admin')->group(function () {
    
    // Entry point with authentication check
    Route::get('/', function() {
        return view('admin.entry');
    })->name('admin.entry');
    
    // Dashboard with real data from database
    Route::get('/dashboard', function() {
        // Get real outlet data
        $outlet = \App\Models\Outlet::first();
        
        if (!$outlet) {
            return 'No outlet found. Please run: php artisan db:seed';
        }
        
        // Real statistics
        $today = \Carbon\Carbon::today();
        $todaySales = \App\Models\Sale::where('outlet_id', $outlet->id)
            ->whereDate('sold_at', $today)
            ->sum('total');
            
        $todayTransactions = \App\Models\Sale::where('outlet_id', $outlet->id)
            ->whereDate('sold_at', $today)
            ->count();
            
        $thisMonth = \Carbon\Carbon::now()->startOfMonth();
        $monthSales = \App\Models\Sale::where('outlet_id', $outlet->id)
            ->where('sold_at', '>=', $thisMonth)
            ->sum('total');
            
        $totalProducts = \App\Models\Product::where('outlet_id', $outlet->id)->count();
        $lowStockProducts = \App\Models\Product::where('outlet_id', $outlet->id)
            ->where('stock', '<=', 5)
            ->count();
            
        // Real recent sales
        $recentSales = \App\Models\Sale::with(['items.product', 'cashier'])
            ->where('outlet_id', $outlet->id)
            ->orderBy('sold_at', 'desc')
            ->limit(10)
            ->get();
            
        // Real sales chart data (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $sales = \App\Models\Sale::where('outlet_id', $outlet->id)
                ->whereDate('sold_at', $date)
                ->sum('total');
            $chartData[] = [
                'date' => $date->format('M j'),
                'sales' => $sales
            ];
        }
        
        return view('admin.dashboard', compact(
            'todaySales', 'todayTransactions', 'monthSales', 
            'totalProducts', 'lowStockProducts', 'recentSales', 'chartData'
        ));
    })->name('admin.dashboard');
    
    // Products Management with real data
    Route::get('products', function() {
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            return 'No outlet found. Please run: php artisan db:seed';
        }
        
        // Real categories from database (categories are global)
        $categories = \App\Models\Category::all();
        
        // Real products from database with pagination
        $products = \App\Models\Product::with('category')
            ->where('outlet_id', $outlet->id)
            ->orderBy('name')
            ->paginate(20);
        
        return view('admin.products.index', compact('products', 'categories'));
    })->name('admin.products.index');
    
    Route::get('products/create', function() {
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            return 'No outlet found. Please run: php artisan db:seed';
        }
        
        $categories = \App\Models\Category::all();
        return view('admin.products.create', compact('categories'));
    })->name('admin.products.create');
    
    Route::post('products', function(\Illuminate\Http\Request $request) {
        $outlet = \App\Models\Outlet::first();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $validated['outlet_id'] = $outlet->id;
        $validated['is_active'] = $request->has('is_active');
        
        \App\Models\Product::create($validated);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product "' . $request->name . '" created successfully!');
    })->name('admin.products.store');
    
    // Sales Management with real data
    Route::get('sales', function(\Illuminate\Http\Request $request) {
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            return 'No outlet found. Please run: php artisan db:seed';
        }
        
        // Date filters
        $startDate = $request->input('start_date') 
            ? \Carbon\Carbon::parse($request->input('start_date'))
            : \Carbon\Carbon::today()->subDays(30);
            
        $endDate = $request->input('end_date')
            ? \Carbon\Carbon::parse($request->input('end_date'))->endOfDay()
            : \Carbon\Carbon::today()->endOfDay();
            
        // Real sales query
        $salesQuery = \App\Models\Sale::with(['items.product', 'cashier', 'payments'])
            ->where('outlet_id', $outlet->id)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->orderBy('sold_at', 'desc');
            
        // Filter by cashier if provided
        if ($cashierId = $request->input('cashier_id')) {
            $salesQuery->where('cashier_id', $cashierId);
        }
        
        // Payment method filter
        if ($paymentMethod = $request->input('payment_method')) {
            $salesQuery->whereHas('payments', function ($query) use ($paymentMethod) {
                $query->where('method', $paymentMethod);
            });
        }
        
        // Get paginated results
        $sales = $salesQuery->paginate(20);
        
        // Summary statistics
        $summary = [
            'total_sales' => $salesQuery->sum('total'),
            'count' => $salesQuery->count(),
            'avg_sale' => $salesQuery->avg('total') ?? 0,
        ];
        
        // Payment method breakdown
        $paymentBreakdown = \App\Models\Payment::whereHas('sale', function ($query) use ($outlet, $startDate, $endDate) {
            $query->where('outlet_id', $outlet->id)
                ->whereBetween('sold_at', [$startDate, $endDate]);
        })
        ->select('method', \DB::raw('SUM(amount) as total'))
        ->groupBy('method')
        ->get();
        
        // Get cashiers for filter
        $cashiers = \App\Models\User::where('outlet_id', $outlet->id)
            ->whereIn('role', ['cashier', 'supervisor', 'owner'])
            ->get();
        
        // Top selling products
        $topProducts = \DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                \DB::raw('SUM(sale_items.qty) as total_qty'),
                \DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->where('sales.outlet_id', $outlet->id)
            ->whereBetween('sales.sold_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.sales.index', compact(
            'sales', 'summary', 'paymentBreakdown', 'cashiers', 
            'topProducts', 'startDate', 'endDate'
        ));
    })->name('admin.sales.index');
    
    Route::get('reports', function(\Illuminate\Http\Request $request) {
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            return 'No outlet found. Please run: php artisan db:seed';
        }
        
        // Date range for reports
        $startDate = $request->input('start_date') 
            ? \Carbon\Carbon::parse($request->input('start_date'))
            : \Carbon\Carbon::today()->startOfMonth();
            
        $endDate = $request->input('end_date')
            ? \Carbon\Carbon::parse($request->input('end_date'))->endOfDay()
            : \Carbon\Carbon::today()->endOfDay();
            
        // Daily sales chart data
        $dailySales = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $dailySales[$date] = 0;
            $currentDate->addDay();
        }
        
        // Fetch actual sales data
        $salesData = \App\Models\Sale::where('outlet_id', $outlet->id)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->select(
                \DB::raw('DATE(sold_at) as date'),
                \DB::raw('SUM(total) as total_sales'),
                \DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('date')
            ->get();
            
        // Populate sales data
        foreach ($salesData as $data) {
            $dailySales[$data->date] = $data->total_sales;
        }
        
        // Payment method breakdown
        $paymentMethods = \App\Models\Payment::whereHas('sale', function ($query) use ($outlet, $startDate, $endDate) {
            $query->where('outlet_id', $outlet->id)
                ->whereBetween('sold_at', [$startDate, $endDate]);
        })
        ->select('method', \DB::raw('SUM(amount) as total'))
        ->groupBy('method')
        ->get();
        
        // Top selling products for the period
        $topProducts = \DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                \DB::raw('SUM(sale_items.qty) as total_qty'),
                \DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->where('sales.outlet_id', $outlet->id)
            ->whereBetween('sales.sold_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();
            
        // Hourly distribution
        $hourlyDistribution = \App\Models\Sale::where('outlet_id', $outlet->id)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->select(
                \DB::raw('HOUR(sold_at) as hour'),
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total) as total')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        // Format hourly data
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = [
                'count' => 0,
                'total' => 0
            ];
        }
        
        foreach ($hourlyDistribution as $hour) {
            $hourlyData[$hour->hour] = [
                'count' => $hour->count,
                'total' => $hour->total
            ];
        }
        
        return view('admin.reports.index', compact(
            'dailySales', 'paymentMethods', 'topProducts', 
            'hourlyData', 'startDate', 'endDate'
        ));
    })->name('admin.reports');
    
    // Users Management with real data
    Route::get('users', function() {
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            return 'No outlet found. Please run: php artisan db:seed';
        }
        
        // Real users from database
        $users = \App\Models\User::where('outlet_id', $outlet->id)
            ->orderBy('name')
            ->paginate(20);
            
        return view('admin.users.index', compact('users'));
    })->name('admin.users.index');
    
    Route::get('users/create', function() {
        return view('admin.users.create');
    })->name('admin.users.create');
    
    Route::post('users', function(\Illuminate\Http\Request $request) {
        $outlet = \App\Models\Outlet::first();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:owner,cashier,supervisor'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        // Always set outlet_id to current outlet
        $validated['outlet_id'] = $outlet->id;
        $validated['password'] = \Hash::make($validated['password']);
        $validated['is_active'] = true;
        
        // Remove password_confirmation from data to be saved
        unset($validated['password_confirmation']);
        
        \App\Models\User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $request->name . '" created successfully!');
    })->name('admin.users.store');
    
    Route::get('users/{user}/edit', function(\App\Models\User $user) {
        $outlet = \App\Models\Outlet::first();
        if ($user->outlet_id !== $outlet->id) {
            abort(403, 'Unauthorized access to user.');
        }
        return view('admin.users.edit', compact('user'));
    })->name('admin.users.edit');
    
    Route::put('users/{user}', function(\Illuminate\Http\Request $request, \App\Models\User $user) {
        $outlet = \App\Models\Outlet::first();
        if ($user->outlet_id !== $outlet->id) {
            abort(403, 'Unauthorized access to user.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:owner,cashier,supervisor'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" updated successfully!');
    })->name('admin.users.update');
    
    // Profile Management Routes
    Route::middleware('auth.admin')->group(function () {
        Route::get('profile', [\App\Http\Controllers\AdminUserController::class, 'profile'])->name('admin.profile');
        Route::put('profile', [\App\Http\Controllers\AdminUserController::class, 'updateProfile'])->name('admin.profile.update');
        Route::put('profile/password', [\App\Http\Controllers\AdminUserController::class, 'changePassword'])->name('admin.profile.change-password');
        Route::put('profile/avatar', [\App\Http\Controllers\AdminUserController::class, 'updateAvatar'])->name('admin.profile.avatar');
    });
    
    // Settings Management Routes
    Route::middleware('auth.admin')->group(function () {
        Route::get('settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings');
        Route::put('settings/outlet', [\App\Http\Controllers\AdminSettingsController::class, 'updateOutlet'])->name('admin.settings.outlet.update');
        Route::put('settings', [\App\Http\Controllers\AdminSettingsController::class, 'updateSettings'])->name('admin.settings.update');
        
        // Category management
        Route::post('settings/categories', [\App\Http\Controllers\AdminSettingsController::class, 'createCategory'])->name('admin.settings.categories.create');
        Route::put('settings/categories/{category}', [\App\Http\Controllers\AdminSettingsController::class, 'updateCategory'])->name('admin.settings.categories.update');
        Route::delete('settings/categories/{category}', [\App\Http\Controllers\AdminSettingsController::class, 'deleteCategory'])->name('admin.settings.categories.delete');
        
        // System tools
        Route::post('settings/backup', [\App\Http\Controllers\AdminSettingsController::class, 'backup'])->name('admin.settings.backup');
        Route::delete('settings/cache', [\App\Http\Controllers\AdminSettingsController::class, 'clearCache'])->name('admin.settings.cache.clear');
    });
});
