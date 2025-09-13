<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Kasir Lite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-6xl font-bold text-gray-800 mb-4">
                <span class="text-blue-600">Kasir</span> Lite
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Lightweight POS System for Indonesian UMKM
            </p>
            <div class="flex justify-center space-x-4">
                <a href="/pos" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                    Open POS System
                </a>
                <a href="/admin" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                    Admin Dashboard
                </a>
                <a href="/receipt/1" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                    View Sample Receipt
                </a>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L12 21l7.5-2.5L17 13"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Multi-Payment Support</h3>
                <p class="text-gray-600">CASH, QRIS, EDC, Transfer, E-Wallet - semua metode pembayaran dalam satu sistem.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Thermal Receipt Printing</h3>
                <p class="text-gray-600">Struk thermal 58mm/80mm yang dapat langsung dicetak dari browser.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Cash Session Management</h3>
                <p class="text-gray-600">Kelola sesi kasir dengan perhitungan selisih otomatis dan tracking shift.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Real-time Stock Management</h3>
                <p class="text-gray-600">Stok otomatis berkurang saat transaksi dengan tracking pergerakan barang.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Secure & Role-based</h3>
                <p class="text-gray-600">Laravel Sanctum authentication dengan role cashier, supervisor, dan owner.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1h2a1 1 0 011-1z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Invoice Management</h3>
                <p class="text-gray-600">Auto-increment invoice dengan format CBB/202509/0001 per outlet per bulan.</p>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Demo Credentials</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">👑 Owner Account</h3>
                    <p class="text-gray-600 mb-1"><strong>Email:</strong> owner@demo.local</p>
                    <p class="text-gray-600 mb-1"><strong>Password:</strong> password</p>
                    <p class="text-gray-600"><strong>Role:</strong> owner</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">💼 Cashier Account</h3>
                    <p class="text-gray-600 mb-1"><strong>Email:</strong> cashier@demo.local</p>
                    <p class="text-gray-600 mb-1"><strong>Password:</strong> password</p>
                    <p class="text-gray-600"><strong>Role:</strong> cashier</p>
                </div>
            </div>
        </div>

        <!-- Sample Products -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Sample Products</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800">Es Teh Manis</h4>
                    <p class="text-sm text-gray-600">SKU: DRK001</p>
                    <p class="text-sm text-gray-600">Barcode: 8901234567890</p>
                    <p class="text-lg font-bold text-blue-600">Rp 5.000</p>
                    <p class="text-sm text-gray-500">Stok: 100 → 98 (after demo sale)</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800">Es Jeruk</h4>
                    <p class="text-sm text-gray-600">SKU: DRK002</p>
                    <p class="text-sm text-gray-600">Barcode: 8901234567891</p>
                    <p class="text-lg font-bold text-blue-600">Rp 7.000</p>
                    <p class="text-sm text-gray-500">Stok: 50</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800">Nasi Goreng</h4>
                    <p class="text-sm text-gray-600">SKU: FD001</p>
                    <p class="text-sm text-gray-600">Barcode: 8901234567892</p>
                    <p class="text-lg font-bold text-blue-600">Rp 15.000</p>
                    <p class="text-sm text-gray-500">Stok: 30 → 29 (after demo sale)</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800">Mie Ayam</h4>
                    <p class="text-sm text-gray-600">SKU: FD002</p>
                    <p class="text-sm text-gray-600">Barcode: 8901234567893</p>
                    <p class="text-lg font-bold text-blue-600">Rp 12.000</p>
                    <p class="text-sm text-gray-500">Stok: 25</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-16 text-gray-600">
            <p>&copy; 2025 Kasir Lite - Built with ❤️ for Indonesian UMKM</p>
            <p class="text-sm mt-2">Powered by Laravel 10 + Alpine.js + Tailwind CSS</p>
        </footer>
    </div>
</body>
</html>