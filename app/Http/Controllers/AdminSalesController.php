<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminSalesController extends Controller
{
    public function index(Request $request)
    {
        $outlet = auth()->user()->outlet;
        
        // Date filters
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::today()->subDays(30);
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();
            
        // Sales query
        $salesQuery = Sale::with(['items.product', 'cashier', 'payments'])
            ->where('outlet_id', $outlet->id)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->orderBy('sold_at', 'desc');
            
        // Filter by cashier
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
        $paymentBreakdown = Payment::whereHas('sale', function ($query) use ($outlet, $startDate, $endDate) {
            $query->where('outlet_id', $outlet->id)
                ->whereBetween('sold_at', [$startDate, $endDate]);
        })
        ->select('method', DB::raw('SUM(amount) as total'))
        ->groupBy('method')
        ->get();
        
        // Get cashiers for filter
        $cashiers = $outlet->cashiers;
        
        // Top selling products
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales')
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
    }
    
    public function show(Sale $sale)
    {
        $this->authorizeSale($sale);
        
        $sale->load(['items.product', 'cashier', 'payments', 'outlet']);
        
        return view('admin.sales.show', compact('sale'));
    }
    
    public function reports(Request $request)
    {
        $outlet = auth()->user()->outlet;
        
        // Date range
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::today()->startOfMonth();
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();
            
        // Daily sales chart data
        $dailySales = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $dailySales[$date] = 0;
            $currentDate->addDay();
        }
        
        // Fetch actual sales data
        $salesData = Sale::where('outlet_id', $outlet->id)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(sold_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('date')
            ->get();
            
        // Populate sales data
        foreach ($salesData as $data) {
            $dailySales[$data->date] = $data->total_sales;
        }
        
        // Payment method breakdown
        $paymentMethods = Payment::whereHas('sale', function ($query) use ($outlet, $startDate, $endDate) {
            $query->where('outlet_id', $outlet->id)
                ->whereBetween('sold_at', [$startDate, $endDate]);
        })
        ->select('method', DB::raw('SUM(amount) as total'))
        ->groupBy('method')
        ->get();
        
        // Top selling products for the period
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->where('sales.outlet_id', $outlet->id)
            ->whereBetween('sales.sold_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();
            
        // Hourly distribution
        $hourlyDistribution = Sale::where('outlet_id', $outlet->id)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(sold_at) as hour'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
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
        
        return view('admin.sales.reports', compact(
            'dailySales', 'paymentMethods', 'topProducts', 
            'hourlyData', 'startDate', 'endDate'
        ));
    }
    
    private function authorizeSale(Sale $sale)
    {
        if ($sale->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Unauthorized access to sale.');
        }
    }
}