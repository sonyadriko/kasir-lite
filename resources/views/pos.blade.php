<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Lite - POS System</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        [x-cloak] { display: none !important; }
        .loading { opacity: 0.5; pointer-events: none; }
        @media (max-width: 768px) {
            .pos-container { flex-direction: column; }
            .product-panel { width: 100%; }
            .payment-panel { width: 100%; position: fixed; bottom: 0; left: 0; right: 0; max-height: 50vh; overflow-y: auto; z-index: 50; }
        }
    </style>
</head>
<body class="bg-gray-50" x-data="posSystem()" x-init="init()" x-cloak>
    <!-- Loading Indicator -->
    <div x-show="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span>Loading...</span>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-blue-600 text-white p-3 shadow-lg relative z-40">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">Kasir Lite</h1>
                    <p class="text-blue-200 text-sm">{{ $outlet->name ?? 'POS System' }}</p>
                </div>
                <!-- Cash Session Status -->
                <div class="hidden sm:block">
                    <div x-show="cashSession.active" class="bg-green-500 px-3 py-1 rounded-full text-xs">
                        ✓ Kasir Aktif
                    </div>
                    <div x-show="!cashSession.active" class="bg-red-500 px-3 py-1 rounded-full text-xs">
                        ✗ Kasir Tutup
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-blue-200 text-xs">Cashier</p>
                    <p class="font-semibold text-sm" x-text="currentUser?.name || '{{ $user->name ?? 'Demo User' }}'"></p>
                </div>
                <!-- Mobile Menu Toggle -->
                <button @click="showPaymentPanel = !showPaymentPanel" 
                        class="md:hidden bg-blue-700 hover:bg-blue-800 px-3 py-2 rounded text-sm">
                    <span x-text="showPaymentPanel ? 'Hide Payment' : 'Show Payment'"></span>
                </button>
                <button @click="logout()" x-show="!isDevelopment" 
                        class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                    Logout
                </button>
                <a href="/" class="bg-blue-700 hover:bg-blue-800 px-3 py-2 rounded text-sm">
                    Home
                </a>
            </div>
        </div>
    </header>

    <div class="flex pos-container min-h-screen" :class="{'pt-20': true}">
        <!-- Left Panel - Product Search & Cart -->
        <div class="product-panel flex-1 p-3 sm:p-4 md:p-6 overflow-y-auto">
            <!-- Quick Actions Bar -->
            <div class="flex flex-wrap gap-2 mb-4">
                <button @click="openCashSession()" x-show="!cashSession.active" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                    🔓 Buka Kasir
                </button>
                <button @click="closeCashSession()" x-show="cashSession.active" 
                        class="bg-purple-800 hover:bg-purple-900 text-white px-4 py-2 rounded-lg text-sm">
                    🔒 Tutup Kasir
                </button>
                <button @click="clearCart()" :disabled="cart.length === 0"
                        class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm">
                    🗑️ Clear
                </button>
                <div class="text-sm bg-blue-100 px-3 py-2 rounded-lg">
                    Items: <span class="font-bold" x-text="cart.length"></span>
                </div>
            </div>

            <!-- Search Products -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <h2 class="text-lg font-semibold mb-3">🔍 Cari Produk</h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input 
                        type="text" 
                        x-model="search"
                        @input="searchProducts()"
                        placeholder="Cari nama produk atau SKU..."
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            x-model="barcode"
                            @keyup.enter="searchByBarcode()"
                            placeholder="Barcode"
                            class="w-32 sm:w-40 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <button 
                            @click="searchByBarcode()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm whitespace-nowrap"
                        >
                            📷 Scan
                        </button>
                    </div>
                </div>
                <!-- Search Results Count -->
                <div x-show="products.length > 0" class="text-xs text-gray-500 mt-2">
                    Ditemukan <span x-text="products.length"></span> produk
                </div>
            </div>

            <!-- Product List -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <h3 class="text-lg font-semibold mb-3">📦 Produk (<span x-text="products.length"></span>)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 max-h-64 sm:max-h-72 overflow-y-auto">
                    <template x-for="product in products" :key="product.id">
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-colors duration-200"
                             @click="addToCart(product)">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-800 text-sm leading-tight" x-text="product.name"></h4>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded" x-text="product.category?.name || 'No Cat'"></span>
                            </div>
                            <p class="text-xs text-gray-500 mb-1">SKU: <span x-text="product.sku || 'N/A'"></span></p>
                            <div class="flex justify-between items-center">
                                <p class="text-lg font-bold text-blue-600">Rp <span x-text="formatCurrency(product.price)"></span></p>
                                <div class="text-right">
                                    <p class="text-xs" :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                        Stok: <span x-text="product.stock"></span>
                                    </p>
                                    <button x-show="product.stock > 0" 
                                            class="text-xs bg-blue-600 text-white px-2 py-1 rounded mt-1 hover:bg-blue-700"
                                            @click.stop="addToCart(product)">
                                        + Tambah
                                    </button>
                                    <span x-show="product.stock <= 0" class="text-xs text-red-500">Habis</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="products.length === 0 && !isLoading" class="col-span-full text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">🔍</div>
                        <p>Tidak ada produk ditemukan</p>
                        <p class="text-sm">Coba kata kunci lain</p>
                    </div>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold">🛍️ Keranjang (<span x-text="cart.length"></span>)</h3>
                    <div class="text-sm text-gray-600">
                        Total: Rp <span class="font-bold text-blue-600" x-text="formatCurrency(subtotal)"></span>
                    </div>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 bg-gray-50 rounded-lg space-y-2 sm:space-y-0">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm truncate" x-text="item.name"></h4>
                                <p class="text-xs text-gray-600">@Rp <span x-text="formatCurrency(item.price)"></span></p>
                                <!-- Discount per item -->
                                <div class="mt-1">
                                    <label class="text-xs text-gray-500">Diskon:</label>
                                    <input type="number" x-model="item.discount" 
                                           @input="updateItemDiscount(index, $event.target.value)"
                                           class="w-20 text-xs border border-gray-300 rounded px-1 py-0.5 ml-1" 
                                           placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end space-x-2 w-full sm:w-auto">
                                <div class="flex items-center space-x-1">
                                    <button @click="updateQuantity(index, item.qty - 1)" 
                                            class="bg-red-500 hover:bg-red-600 text-white w-7 h-7 rounded text-sm flex items-center justify-center">−</button>
                                    <input type="number" x-model="item.qty" 
                                           @input="updateQuantity(index, $event.target.value)"
                                           class="w-12 text-center text-sm border border-gray-300 rounded py-1" 
                                           min="1">
                                    <button @click="updateQuantity(index, item.qty + 1)" 
                                            class="bg-green-500 hover:bg-green-600 text-white w-7 h-7 rounded text-sm flex items-center justify-center">+</button>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-sm">Rp <span x-text="formatCurrency(item.total)"></span></div>
                                    <button @click="removeFromCart(index)" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs mt-1">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="text-center text-gray-500 py-8">
                        <div class="text-4xl mb-2">🛍️</div>
                        <p>Keranjang kosong</p>
                        <p class="text-sm">Tambahkan produk untuk mulai transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Payment -->
        <div class="payment-panel w-full md:w-1/3 bg-white shadow-lg" 
             :class="{'hidden': !showPaymentPanel && window.innerWidth < 768}"
             x-show="showPaymentPanel || window.innerWidth >= 768">
            <div class="p-4 md:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg md:text-xl font-semibold">💳 Pembayaran</h2>
                    <button @click="showPaymentPanel = false" class="md:hidden text-gray-500 hover:text-gray-700">
                        ✕
                    </button>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-700 mb-3">📊 Ringkasan Pesanan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Subtotal:</span>
                            <span class="font-semibold">Rp <span x-text="formatCurrency(subtotal)"></span></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Diskon Total:</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs">Rp</span>
                                <input type="number" x-model="discountTotal" @input="calculateTotals()"
                                       class="w-20 text-right border border-gray-300 rounded px-2 py-1 text-sm"
                                       min="0" step="1000" placeholder="0">
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Pajak:</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" x-model="taxPercent" @input="calculateTotals()"
                                       class="w-16 text-right border border-gray-300 rounded px-2 py-1 text-sm"
                                       min="0" max="100" step="0.5" placeholder="0">
                                <span class="text-xs">%</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Pembulatan:</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs">Rp</span>
                                <input type="number" x-model="rounding" @input="calculateTotals()"
                                       class="w-20 text-right border border-gray-300 rounded px-2 py-1 text-sm"
                                       step="100" placeholder="0">
                            </div>
                        </div>
                        <hr class="border-gray-300">
                        <div class="flex justify-between items-center text-lg font-bold bg-blue-50 p-3 rounded">
                            <span class="text-blue-800">TOTAL:</span>
                            <span class="text-blue-800">Rp <span x-text="formatCurrency(total)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-4">
                    <h3 class="font-semibold mb-3">💵 Metode Pembayaran</h3>
                    <div class="space-y-2 mb-3" x-show="payments.length > 0">
                        <template x-for="(payment, index) in payments" :key="index">
                            <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800" x-text="payment.method"></span>
                                    <span class="font-semibold">Rp <span x-text="formatCurrency(payment.amount)"></span></span>
                                </div>
                                <button @click="removePayment(index)" 
                                        class="text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50">×</button>
                            </div>
                        </template>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
                            <select x-model="paymentMethod" class="border border-gray-300 rounded px-3 py-2 text-sm">
                                <option value="CASH">💵 Cash</option>
                                <option value="QRIS">📱 QRIS</option>
                                <option value="EDC">💳 EDC</option>
                                <option value="TRANSFER">🏦 Transfer</option>
                                <option value="EWALLET">📦 E-Wallet</option>
                            </select>
                            <input type="number" x-model="paymentAmount" placeholder="Jumlah"
                                   @keyup.enter="addPayment()"
                                   class="border border-gray-300 rounded px-3 py-2 text-sm">
                            <button @click="addPayment()" :disabled="!paymentAmount || paymentAmount <= 0"
                                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded text-sm">
                                + Tambah
                            </button>
                        </div>
                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-4 gap-1" x-show="paymentMethod === 'CASH'">
                            <template x-for="amount in [10000, 20000, 50000, 100000]" :key="amount">
                                <button @click="paymentAmount = amount" 
                                        class="bg-gray-100 hover:bg-gray-200 text-xs py-2 rounded border">
                                    <span x-text="formatCurrency(amount)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="mb-4 p-4 rounded-lg" 
                     :class="totalPaid >= total ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'">
                    <h3 class="font-semibold mb-2" :class="totalPaid >= total ? 'text-green-700' : 'text-yellow-700'">
                        📈 Status Pembayaran
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Total yang harus dibayar:</span>
                            <span class="font-bold">Rp <span x-text="formatCurrency(total)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Total dibayar:</span>
                            <span class="font-bold" :class="totalPaid >= total ? 'text-green-600' : 'text-red-600'">
                                Rp <span x-text="formatCurrency(totalPaid)"></span>
                            </span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold" 
                             :class="change >= 0 ? 'text-green-600' : 'text-red-600'">
                            <span x-text="change >= 0 ? '✓ Kembalian:' : '⚠️ Kurang:'"></span>
                            <span>Rp <span x-text="formatCurrency(Math.abs(change))"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <!-- Main Process Button -->
                    <button @click="processPayment()" 
                            :disabled="cart.length === 0 || totalPaid < total || isLoading"
                            :class="cart.length === 0 || totalPaid < total || isLoading ? 
                                    'bg-gray-400 cursor-not-allowed' : 
                                    'bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-xl'"
                            class="w-full text-white px-6 py-4 rounded-lg font-bold text-lg transition-all duration-200">
                        <span x-show="!isLoading">
                            <span x-text="cart.length === 0 ? '🛍️ Tambah Produk Dulu' : totalPaid < total ? '💰 Bayar Dulu' : '🚀 Proses Pembayaran'"></span>
                        </span>
                        <span x-show="isLoading" class="flex items-center justify-center space-x-2">
                            <div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                            <span>Processing...</span>
                        </span>
                    </button>
                    
                    <!-- Secondary Actions -->
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="clearCart()" :disabled="cart.length === 0"
                                class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded text-sm">
                            🗑️ Clear
                        </button>
                        <button @click="showCashSession = !showCashSession"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm">
                            💼 Kasir
                        </button>
                    </div>

                    <!-- Cash Session Controls -->
                    <div x-show="showCashSession" x-transition class="p-4 border border-purple-200 bg-purple-50 rounded-lg">
                        <h4 class="font-semibold text-purple-800 mb-3">💼 Manajemen Kasir</h4>
                        
                        <div x-show="!cashSession.active" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Awal Kasir</label>
                                <input type="number" x-model="openingCash" placeholder="200000"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                            <button @click="openCashSession()" :disabled="!openingCash || openingCash <= 0"
                                    class="w-full bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white px-4 py-2 rounded text-sm">
                                🔓 Buka Kasir
                            </button>
                        </div>
                        
                        <div x-show="cashSession.active" class="space-y-3">
                            <div class="text-sm text-purple-700">
                                <p>✓ Kasir dibuka: <span class="font-medium" x-text="cashSession.opened_at"></span></p>
                                <p>Saldo awal: <span class="font-medium">Rp <span x-text="formatCurrency(openingCash)"></span></span></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Akhir (Hitung Manual)</label>
                                <input type="number" x-model="closingCash" placeholder="Hitung uang di laci"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                            <button @click="closeCashSession()" :disabled="!closingCash"
                                    class="w-full bg-purple-800 hover:bg-purple-900 disabled:bg-gray-400 text-white px-4 py-2 rounded text-sm">
                                🔒 Tutup Kasir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-green-600 mb-4">Transaksi Berhasil!</h3>
            <div class="space-y-2 mb-4">
                <p><strong>Invoice:</strong> <span x-text="lastSale.invoice_no"></span></p>
                <p><strong>Total:</strong> Rp <span x-text="formatCurrency(lastSale.total)"></span></p>
                <p><strong>Dibayar:</strong> Rp <span x-text="formatCurrency(lastSale.paid)"></span></p>
                <p><strong>Kembalian:</strong> Rp <span x-text="formatCurrency(lastSale.change)"></span></p>
            </div>
            <div class="flex space-x-2">
                <button @click="printReceipt()" 
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded">
                    Print Struk
                </button>
                <button @click="closeSuccessModal()" 
                        class="flex-1 bg-gray-600 text-white px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                // Products
                products: [],
                search: '',
                barcode: '',
                
                // Cart
                cart: [],
                
                // Payment
                subtotal: 0,
                discountTotal: 0,
                taxPercent: 10,
                rounding: 0,
                total: 0,
                
                payments: [],
                paymentMethod: 'CASH',
                paymentAmount: '',
                totalPaid: 0,
                change: 0,
                
                // Cash Session
                cashSession: {
                    active: false,
                    opened_at: '',
                },
                openingCash: 200000,
                closingCash: '',
                
                // UI State
                showSuccessModal: false,
                showCashSession: false,
                showPaymentPanel: true,
                isLoading: false,
                lastSale: {},
                
                // Authentication
                apiToken: null,
                currentUser: null,
                isDevelopment: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1',

                async init() {
                    // Check authentication first
                    if (!this.isDevelopment && !this.checkAuth()) {
                        window.location.href = '/login';
                        return;
                    }
                    
                    await this.loadProducts();
                    if (!this.isDevelopment) {
                        await this.checkCashSession();
                    }
                },

                checkAuth() {
                    const token = localStorage.getItem('pos_token');
                    const user = localStorage.getItem('pos_user');
                    const expires = localStorage.getItem('pos_token_expires');
                    
                    if (!token || !user || !expires) {
                        return false;
                    }
                    
                    // Check if token expired
                    if (new Date().getTime() > parseInt(expires)) {
                        this.clearAuth();
                        return false;
                    }
                    
                    this.apiToken = token;
                    this.currentUser = JSON.parse(user);
                    return true;
                },

                clearAuth() {
                    localStorage.removeItem('pos_token');
                    localStorage.removeItem('pos_user');
                    localStorage.removeItem('pos_token_expires');
                    this.apiToken = null;
                    this.currentUser = null;
                },

                async loadProducts() {
                    try {
                        const endpoint = this.isDevelopment ? '/api/dev/products' : '/api/products';
                        const headers = { 'Accept': 'application/json' };
                        
                        if (!this.isDevelopment && this.apiToken) {
                            headers['Authorization'] = `Bearer ${this.apiToken}`;
                        }
                        
                        const response = await fetch(endpoint, { headers });
                        const data = await response.json();
                        this.products = data.data || [];
                    } catch (error) {
                        console.error('Error loading products:', error);
                        // Fallback to demo products if API fails
                        this.products = this.getDemoProducts();
                    }
                },

                getDemoProducts() {
                    return [
                        { id: 1, name: 'Kopi Hitam', price: 8000, stock: 50, sku: 'KOPI001', category: { name: 'Minuman' } },
                        { id: 2, name: 'Teh Manis', price: 5000, stock: 30, sku: 'TEH001', category: { name: 'Minuman' } },
                        { id: 3, name: 'Nasi Gudeg', price: 15000, stock: 20, sku: 'NSI001', category: { name: 'Makanan' } },
                        { id: 4, name: 'Ayam Bakar', price: 25000, stock: 15, sku: 'AYM001', category: { name: 'Makanan' } },
                        { id: 5, name: 'Es Jeruk', price: 7000, stock: 40, sku: 'JRK001', category: { name: 'Minuman' } },
                        { id: 6, name: 'Gado-Gado', price: 12000, stock: 25, sku: 'GDO001', category: { name: 'Makanan' } }
                    ];
                },

                async searchProducts() {
                    if (!this.search.trim()) {
                        await this.loadProducts();
                        return;
                    }
                    
                    try {
                        const endpoint = this.isDevelopment ? '/api/dev/products' : '/api/products';
                        const headers = { 'Accept': 'application/json' };
                        
                        if (!this.isDevelopment && this.apiToken) {
                            headers['Authorization'] = `Bearer ${this.apiToken}`;
                        }
                        
                        const response = await fetch(`${endpoint}?search=${encodeURIComponent(this.search)}`, { headers });
                        const data = await response.json();
                        this.products = data.data || [];
                    } catch (error) {
                        console.error('Error searching products:', error);
                        // Fallback: filter demo products
                        const demoProducts = this.getDemoProducts();
                        this.products = demoProducts.filter(p => 
                            p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            p.sku.toLowerCase().includes(this.search.toLowerCase())
                        );
                    }
                },

                async searchByBarcode() {
                    if (!this.barcode.trim()) return;
                    
                    try {
                        const response = await fetch(`/api/products?barcode=${encodeURIComponent(this.barcode)}`, {
                            headers: {
                                'Authorization': `Bearer ${this.apiToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.data && data.data.length > 0) {
                            this.addToCart(data.data[0]);
                            this.barcode = '';
                        } else {
                            alert('Produk dengan barcode tersebut tidak ditemukan');
                        }
                    } catch (error) {
                        console.error('Error searching by barcode:', error);
                    }
                },

                addToCart(product) {
                    const existingIndex = this.cart.findIndex(item => item.product_id === product.id);
                    
                    if (existingIndex >= 0) {
                        this.cart[existingIndex].qty += 1;
                        this.cart[existingIndex].total = this.cart[existingIndex].qty * this.cart[existingIndex].price;
                    } else {
                        this.cart.push({
                            product_id: product.id,
                            name: product.name,
                            price: parseFloat(product.price),
                            qty: 1,
                            discount: 0,
                            total: parseFloat(product.price)
                        });
                    }
                    
                    this.calculateTotals();
                },

                updateQuantity(index, newQty) {
                    if (newQty <= 0) {
                        this.removeFromCart(index);
                        return;
                    }
                    
                    this.cart[index].qty = newQty;
                    this.cart[index].total = this.cart[index].qty * this.cart[index].price;
                    this.calculateTotals();
                },

                updateItemDiscount(index, discount) {
                    this.cart[index].discount = parseFloat(discount) || 0;
                    const priceAfterDiscount = this.cart[index].price - this.cart[index].discount;
                    this.cart[index].total = this.cart[index].qty * Math.max(0, priceAfterDiscount);
                    this.calculateTotals();
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.calculateTotals();
                },

                clearCart() {
                    this.cart = [];
                    this.payments = [];
                    this.discountTotal = 0;
                    this.rounding = 0;
                    this.calculateTotals();
                },

                calculateTotals() {
                    this.subtotal = this.cart.reduce((sum, item) => sum + item.total, 0);
                    
                    const afterDiscount = this.subtotal - (this.discountTotal || 0);
                    const taxAmount = afterDiscount * ((this.taxPercent || 0) / 100);
                    
                    this.total = afterDiscount + taxAmount + (this.rounding || 0);
                    this.totalPaid = this.payments.reduce((sum, payment) => sum + payment.amount, 0);
                    this.change = this.totalPaid - this.total;
                },

                addPayment() {
                    if (!this.paymentAmount || this.paymentAmount <= 0) return;
                    
                    this.payments.push({
                        method: this.paymentMethod,
                        amount: parseFloat(this.paymentAmount)
                    });
                    
                    this.paymentAmount = '';
                    this.calculateTotals();
                },

                removePayment(index) {
                    this.payments.splice(index, 1);
                    this.calculateTotals();
                },

                async processPayment() {
                    if (this.cart.length === 0 || this.totalPaid < this.total) return;
                    
                    this.isLoading = true;
                    
                    if (this.isDevelopment) {
                        // Demo mode - create real sale for receipt
                        try {
                            const response = await fetch('/api/dev/sales', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    outlet_id: 1,
                                    sold_at: new Date().toISOString(),
                                    items: this.cart,
                                    discount_total: this.discountTotal || 0,
                                    tax_percent: this.taxPercent || 0,
                                    rounding: this.rounding || 0,
                                    payments: this.payments,
                                    note: 'Demo transaction'
                                })
                            });
                            
                            if (response.ok) {
                                const result = await response.json();
                                this.lastSale = result;
                            } else {
                                // Fallback to demo data
                                this.lastSale = {
                                    sale_id: 1, // Use existing sale ID
                                    invoice_no: `DEMO/${new Date().getFullYear()}${(new Date().getMonth()+1).toString().padStart(2,'0')}/${Math.floor(Math.random()*9999).toString().padStart(4,'0')}`,
                                    total: this.total,
                                    paid: this.totalPaid,
                                    change: this.change
                                };
                            }
                        } catch (error) {
                            console.error('Demo sale creation failed:', error);
                            // Use existing sale ID as fallback
                            this.lastSale = {
                                sale_id: 1,
                                invoice_no: `DEMO/ERROR/${Date.now()}`,
                                total: this.total,
                                paid: this.totalPaid,
                                change: this.change
                            };
                        }
                        
                        this.showSuccessModal = true;
                        this.clearCart();
                        this.isLoading = false;
                        return;
                    }
                    
                    const saleData = {
                        outlet_id: 1, // Demo outlet ID
                        sold_at: new Date().toISOString(),
                        items: this.cart,
                        discount_total: this.discountTotal || 0,
                        tax_percent: this.taxPercent || 0,
                        rounding: this.rounding || 0,
                        payments: this.payments,
                        note: ''
                    };

                    try {
                        const endpoint = '/api/dev/sales';
                        const headers = {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        };

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers,
                            body: JSON.stringify(saleData)
                        });

                        if (response.ok) {
                            const result = await response.json();
                            this.lastSale = result;
                            this.showSuccessModal = true;
                            this.clearCart();
                            await this.loadProducts(); // Refresh for updated stock
                        } else {
                            const error = await response.json();
                            alert('Error: ' + (error.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error processing payment:', error);
                        alert('Demo mode: Transaksi disimulasikan berhasil!');
                        // Fallback to demo success with existing sale ID
                        this.lastSale = {
                            sale_id: 1, // Use existing sale for receipt
                            invoice_no: `DEMO/ERROR/${Date.now()}`,
                            total: this.total,
                            paid: this.totalPaid,
                            change: this.change
                        };
                        this.showSuccessModal = true;
                        this.clearCart();
                    }
                    
                    this.isLoading = false;
                },

                printReceipt() {
                    if (this.isDevelopment && (!this.lastSale.sale_id || this.lastSale.sale_id === 1)) {
                        // Use demo receipt for development mode
                        window.open('/demo-receipt', '_blank');
                    } else {
                        window.open(`/receipt/${this.lastSale.sale_id}`, '_blank');
                    }
                },

                closeSuccessModal() {
                    this.showSuccessModal = false;
                },

                async openCashSession() {
                    if (!this.openingCash || this.openingCash <= 0) {
                        alert('Masukkan saldo awal kasir yang valid');
                        return;
                    }
                    
                    try {
                        const response = await fetch('/api/cash-sessions/open', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${this.apiToken}`,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ opening_cash: this.openingCash })
                        });

                        if (response.ok) {
                            const result = await response.json();
                            this.cashSession = {
                                active: true,
                                opened_at: new Date(result.opened_at).toLocaleString('id-ID')
                            };
                            this.showCashSession = false; // Hide cash session panel
                            alert('✅ Kasir berhasil dibuka!');
                        } else {
                            const error = await response.json();
                            alert('❌ Error: ' + (error.error || 'Gagal membuka kasir'));
                        }
                    } catch (error) {
                        console.error('Error opening cash session:', error);
                        alert('❌ Terjadi kesalahan saat membuka kasir');
                    }
                },

                async closeCashSession() {
                    if (!this.closingCash && this.closingCash !== 0) {
                        alert('Masukkan saldo akhir kasir');
                        return;
                    }
                    
                    try {
                        const response = await fetch('/api/cash-sessions/close', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${this.apiToken}`,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ closing_cash: this.closingCash })
                        });

                        if (response.ok) {
                            const result = await response.json();
                            this.cashSession.active = false;
                            this.showCashSession = false;
                            this.closingCash = '';
                            
                            const variance = result.variance || 0;
                            const variantText = variance >= 0 ? 
                                `✅ Lebih Rp ${this.formatCurrency(Math.abs(variance))}` : 
                                `❌ Kurang Rp ${this.formatCurrency(Math.abs(variance))}`;
                            
                            alert(`🔒 Kasir ditutup!\n${variantText}`);
                        } else {
                            const error = await response.json();
                            alert('❌ Error: ' + (error.error || 'Gagal menutup kasir'));
                        }
                    } catch (error) {
                        console.error('Error closing cash session:', error);
                        alert('❌ Terjadi kesalahan saat menutup kasir');
                    }
                },

                async checkCashSession() {
                    try {
                        const response = await fetch('/api/cash-sessions/active', {
                            headers: {
                                'Authorization': `Bearer ${this.apiToken}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const result = await response.json();
                            this.cashSession = {
                                active: true,
                                opened_at: new Date(result.opened_at).toLocaleString('id-ID')
                            };
                        } else {
                            // 404 or other error - no active session
                            this.cashSession.active = false;
                        }
                    } catch (error) {
                        // Network error or other issues - no active session
                        this.cashSession.active = false;
                        console.log('Cash session check failed:', error.message);
                    }
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID').format(amount || 0);
                },

                async logout() {
                    if (!confirm('Yakin ingin logout?')) return;
                    
                    try {
                        // Call logout API if not in development
                        if (!this.isDevelopment && this.apiToken) {
                            await fetch('/api/logout', {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${this.apiToken}`,
                                    'Accept': 'application/json'
                                }
                            });
                        }
                    } catch (error) {
                        console.log('Logout API call failed:', error);
                    }
                    
                    // Clear auth data and redirect
                    this.clearAuth();
                    window.location.href = '/login';
                }
            }
        }
    </script>
</body>
</html>