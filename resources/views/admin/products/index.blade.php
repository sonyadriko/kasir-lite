@extends('layouts.admin')

@section('title', 'Product Management')
@section('page-title', 'Product Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">All Products</h2>
            <p class="mt-1 text-gray-600">Manage your product inventory</p>
        </div>
        <a href="{{ route('admin.products.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Product
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" 
                       id="searchInput"
                       placeholder="Search products..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div>
                <select id="stockFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Stock</option>
                    <option value="low">Low Stock (≤5)</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    @if($product->barcode)
                                        <div class="text-sm text-gray-500">Barcode: {{ $product->barcode }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->category->name ?? 'No Category' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($product->price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 mr-2">{{ $product->stock }}</span>
                                    @if($product->stock <= ($product->min_stock ?? 5))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Low Stock
                                        </span>
                                    @elseif($product->stock == 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Out of Stock
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" 
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <button onclick="alert('Product: {{ $product->name }}\nSKU: {{ $product->sku }}\nPrice: Rp {{ number_format($product->price) }}\nStock: {{ $product->stock }}')" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Details</button>
                                            <button onclick="alert('Edit feature coming soon!')" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</button>
                                            <button onclick="adjustStock({{ $product->id }}, '{{ $product->name }}')" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Adjust Stock</button>
                                            <button onclick="if(confirm('Are you sure?')) alert('Delete feature coming soon!')" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                                </svg>
                                <p class="text-lg font-medium">No products found</p>
                                <p class="mt-1">Get started by creating your first product.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Adjust Stock</h3>
            
            <form id="stockForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <p id="productName" class="text-sm text-gray-600"></p>
                </div>
                
                <div class="mb-4">
                    <label for="adjustment" class="block text-sm font-medium text-gray-700 mb-2">
                        Stock Adjustment
                    </label>
                    <input type="number" 
                           name="adjustment" 
                           id="adjustment" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g. +10 or -5" required>
                    <p class="text-xs text-gray-500 mt-1">Enter positive number to add stock, negative to reduce</p>
                </div>
                
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason
                    </label>
                    <input type="text" 
                           name="reason" 
                           id="reason" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g. Damaged goods, New stock arrival" required>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeStockModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Adjust Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function adjustStock(productId, productName) {
        document.getElementById('productName').textContent = productName;
        document.getElementById('stockForm').action = `/admin/products/${productId}/adjust-stock`;
        document.getElementById('stockModal').classList.remove('hidden');
    }
    
    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
        document.getElementById('stockForm').reset();
    }
    
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        const stockFilter = document.getElementById('stockFilter');
        
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryValue = categoryFilter.value;
            const statusValue = statusFilter.value;
            const stockValue = stockFilter.value;
            
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip empty state row
                
                const productName = row.cells[0].textContent.toLowerCase();
                const sku = row.cells[1].textContent.toLowerCase();
                const categoryText = row.cells[2].textContent;
                const statusBadge = row.cells[5].querySelector('span');
                const stockCell = row.cells[4];
                
                let show = true;
                
                // Search filter
                if (searchTerm && !productName.includes(searchTerm) && !sku.includes(searchTerm)) {
                    show = false;
                }
                
                // Category filter
                if (categoryValue && categoryText !== categoryFilter.options[categoryFilter.selectedIndex].text) {
                    show = false;
                }
                
                // Status filter
                if (statusValue !== '') {
                    const isActive = statusBadge.textContent.trim() === 'Active';
                    if ((statusValue === '1' && !isActive) || (statusValue === '0' && isActive)) {
                        show = false;
                    }
                }
                
                // Stock filter
                if (stockValue) {
                    const stockNumber = parseInt(stockCell.querySelector('.text-gray-900').textContent);
                    const hasLowStock = stockCell.querySelector('.bg-red-100');
                    const hasOutOfStock = stockCell.querySelector('.bg-gray-100');
                    
                    if (stockValue === 'low' && !hasLowStock) {
                        show = false;
                    } else if (stockValue === 'out' && stockNumber > 0) {
                        show = false;
                    }
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        searchInput.addEventListener('input', filterTable);
        categoryFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
        stockFilter.addEventListener('change', filterTable);
    });
</script>
@endpush