@extends('layouts.admin')

@section('title', 'Sales Reports & Analytics')
@section('page-title', 'Sales Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Date Filter -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Analytics Dashboard</h2>
                <p class="mt-1 text-gray-600">Comprehensive sales and performance analytics</p>
            </div>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" 
                       id="start_date" 
                       name="start_date" 
                       value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" 
                       id="end_date" 
                       name="end_date" 
                       value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Update Report
                </button>
            </div>
        </form>
    </div>

    <!-- Daily Sales Chart -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Sales Trend</h3>
        <div class="h-96">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <!-- Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Payment Methods Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods Distribution</h3>
            @if($paymentMethods->count() > 0)
                <div class="h-64 flex items-center justify-center">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($paymentMethods as $payment)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">{{ $payment->method }}</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($payment->total) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p>No payment data available</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Top Products -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Products</h3>
            @if($topProducts->count() > 0)
                <div class="space-y-4">
                    @foreach($topProducts as $index => $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-blue-600">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $product->total_qty }} units sold</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">Rp {{ number_format($product->total_sales) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                    </svg>
                    <p>No product data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Hourly Sales Pattern -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Pattern by Hour</h3>
        <div class="h-64">
            <canvas id="hourlyChart"></canvas>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Sales</p>
                    <p class="text-2xl font-bold">Rp {{ number_format(array_sum($dailySales)) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Average Daily</p>
                    <p class="text-2xl font-bold">Rp {{ number_format(count($dailySales) > 0 ? array_sum($dailySales) / count($dailySales) : 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-400 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Best Day</p>
                    <p class="text-2xl font-bold">Rp {{ number_format(max($dailySales)) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-400 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Active Days</p>
                    <p class="text-2xl font-bold">{{ count(array_filter($dailySales, function($sale) { return $sale > 0; })) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-400 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Daily Sales Chart
    const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesData = @json($dailySales);
    
    new Chart(dailySalesCtx, {
        type: 'line',
        data: {
            labels: Object.keys(dailySalesData),
            datasets: [{
                label: 'Daily Sales (Rp)',
                data: Object.values(dailySalesData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
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
            }
        }
    });

    // Payment Methods Chart
    const paymentData = @json($paymentMethods);
    if (paymentData.length > 0) {
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: paymentData.map(p => p.method),
                datasets: [{
                    data: paymentData.map(p => p.total),
                    backgroundColor: [
                        '#3B82F6',
                        '#EF4444',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = @json($hourlyData);
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => i + ':00'),
            datasets: [{
                label: 'Sales Count',
                data: Object.values(hourlyData).map(h => h.count),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
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
                    title: {
                        display: true,
                        text: 'Number of Transactions'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hour of Day'
                    }
                }
            }
        }
    });
</script>
@endpush