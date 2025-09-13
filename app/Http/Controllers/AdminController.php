<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Outlet;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get current user outlet
        $outlet = auth()->user()->outlet;
        
        // Today's stats
        $today = Carbon::today();
        $todaySales = Sale::where('outlet_id', $outlet->id)
            ->whereDate('sold_at', $today)
            ->sum('total');
            
        $todayTransactions = Sale::where('outlet_id', $outlet->id)
            ->whereDate('sold_at', $today)
            ->count();
            
        // This month stats
        $thisMonth = Carbon::now()->startOfMonth();
        $monthSales = Sale::where('outlet_id', $outlet->id)
            ->where('sold_at', '>=', $thisMonth)
            ->sum('total');
            
        // Product stats
        $totalProducts = Product::where('outlet_id', $outlet->id)->count();
        $lowStockProducts = Product::where('outlet_id', $outlet->id)
            ->where('stock', '<=', 5)
            ->count();
            
        // Recent sales
        $recentSales = Sale::with(['items.product', 'cashier'])
            ->where('outlet_id', $outlet->id)
            ->orderBy('sold_at', 'desc')
            ->limit(10)
            ->get();
            
        // Sales chart data (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Sale::where('outlet_id', $outlet->id)
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
    }
    
    public function reports()
    {
        return view('admin.reports');
    }
    
    public function settings()
    {
        $outlet = auth()->user()->outlet;
        return view('admin.settings', compact('outlet'));
    }
}