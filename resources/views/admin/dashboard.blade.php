@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Sales -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($todaySales) }}</h3>
                    <p class="text-gray-600">Today's Sales</p>
                </div>
            </div>
        </div>

        <!-- Today's Transactions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $todayTransactions }}</h3>
                    <p class="text-gray-600">Today's Orders</p>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($monthSales) }}</h3>
                    <p class="text-gray-600">This Month</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-{{ $lowStockProducts > 0 ? 'red' : 'gray' }}-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-{{ $lowStockProducts > 0 ? 'red' : 'gray' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $lowStockProducts }}</h3>
                    <p class="text-gray-600">Low Stock Items</p>
                    @if($lowStockProducts > 0)
                        <a href="{{ route('admin.products.index') }}?filter=low_stock" class="text-red-600 text-sm hover:underline">View items</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Trend (Last 7 Days)</h3>
            <canvas id="salesChart" height="150"></canvas>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                <a href="{{ route('admin.sales.index') }}" class="text-blue-600 hover:underline text-sm">View all</a>
            </div>
            
            <div class="space-y-3">
                @forelse($recentSales as $sale)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900">{{ $sale->invoice_no }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $sale->cashier->name ?? 'N/A' }} • {{ $sale->sold_at->format('M j, H:i') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">Rp {{ number_format($sale->total) }}</div>
                            <div class="text-sm text-gray-600">{{ $sale->items->count() }} items</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>No recent sales found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.products.create') }}" 
               class="flex items-center justify-center p-4 bg-blue-50 rounded-lg border-2 border-dashed border-blue-300 hover:bg-blue-100 transition-colors">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-600">Add Product</span>
                </div>
            </a>
            
            <a href="/pos" 
               class="flex items-center justify-center p-4 bg-green-50 rounded-lg border-2 border-dashed border-green-300 hover:bg-green-100 transition-colors">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L12 21l7.5-2.5L17 13"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-600">Open POS</span>
                </div>
            </a>
            
            <a href="{{ route('admin.reports') }}" 
               class="flex items-center justify-center p-4 bg-purple-50 rounded-lg border-2 border-dashed border-purple-300 hover:bg-purple-100 transition-colors">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-purple-600">View Reports</span>
                </div>
            </a>
            
            <a href="{{ route('admin.users.create') }}" 
               x-show="(() => {
                   try {
                       const user = JSON.parse(localStorage.getItem('pos_user'));
                       return user && user.role === 'owner';
                   } catch {
                       return false;
                   }
               })()"
               class="flex items-center justify-center p-4 bg-yellow-50 rounded-lg border-2 border-dashed border-yellow-300 hover:bg-yellow-100 transition-colors">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span class="text-sm font-medium text-yellow-600">Add User</span>
                </div>
            </a>
            
            <a href="{{ route('admin.settings') }}" 
               class="flex items-center justify-center p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 hover:bg-gray-100 transition-colors">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-600">Settings</span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sales chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [{
                label: 'Sales (Rp)',
                data: chartData.map(item => item.sales),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });
</script>
@endpush