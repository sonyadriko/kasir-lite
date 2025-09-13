<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kasir Lite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">
    <div x-data="{ 
        sidebarOpen: false,
        currentUser: null,
        checkAuth() {
            const token = localStorage.getItem('pos_token');
            const user = localStorage.getItem('pos_user');
            const expires = localStorage.getItem('pos_token_expires');
            
            if (!token || !user) {
                window.location.href = '/login';
                return;
            }
            
            // Check if token is expired
            if (expires && new Date().getTime() > parseInt(expires)) {
                localStorage.removeItem('pos_token');
                localStorage.removeItem('pos_user');
                localStorage.removeItem('pos_token_expires');
                window.location.href = '/login';
                return;
            }
            
            this.currentUser = JSON.parse(user);
        },
        async logout() {
            const token = localStorage.getItem('pos_token');
            
            // Try to logout via API
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } catch (error) {
                console.log('Logout API call failed, but proceeding with local logout');
            }
            
            // Clear local storage
            localStorage.removeItem('pos_token');
            localStorage.removeItem('pos_user');
            localStorage.removeItem('pos_token_expires');
            
            // Redirect to login
            window.location.href = '/login';
        }
    }" 
    x-init="checkAuth()" 
    class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            
            <div class="flex items-center justify-center h-16 px-4 bg-gray-800">
                <h1 class="text-xl font-bold text-white">
                    <span class="text-blue-400">Kasir</span> Admin
                </h1>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8 px-4">
                <div class="space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.dashboard*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7 4-4 4 4"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    <!-- Products -->
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.products*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                        </svg>
                        Products
                    </a>
                    
                    <!-- Sales -->
                    <a href="{{ route('admin.sales.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.sales*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Sales
                    </a>
                    
                    <!-- Reports -->
                    <a href="{{ route('admin.reports') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                    
                    <!-- Users (Only for owners) -->
                    <a href="{{ route('admin.users.index') }}" 
                       x-show="currentUser?.role === 'owner'"
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.users*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Users
                    </a>
                    
                    <hr class="my-4 border-gray-600">
                    
                    <!-- Profile -->
                    <a href="{{ route('admin.profile') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.profile*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                    
                    <!-- Settings -->
                    <a href="{{ route('admin.settings') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-blue-600 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                    
                    <!-- POS System -->
                    <a href="/pos" 
                       class="flex items-center px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L12 21l7.5-2.5L17 13"></path>
                        </svg>
                        Back to POS
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top navigation -->
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="text-gray-500 focus:outline-none focus:text-gray-700 lg:hidden">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <div class="ml-4 lg:ml-0">
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>
                
                <!-- User info -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900" x-text="currentUser?.name || 'User'"></div>
                        <div class="text-xs text-gray-500">
                            <span x-text="currentUser?.role || 'Role'"></span> • 
                            <span x-text="currentUser?.outlet || 'Outlet'"></span>
                        </div>
                    </div>
                    <button @click="logout()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Logout
                    </button>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                
                <!-- Flash messages -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition 
                         class="mx-6 mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button @click="show = false" type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none">
                                        <span class="sr-only">Dismiss</span>
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition 
                         class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button @click="show = false" type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                                        <span class="sr-only">Dismiss</span>
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-30 bg-gray-600 opacity-50 lg:hidden"
             style="display: none;">
        </div>
    </div>
    
    @stack('scripts')
    
    <script>
        // Setup CSRF token for all AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Add CSRF token to all fetch requests
        const originalFetch = window.fetch;
        window.fetch = function() {
            let [url, config = {}] = arguments;
            if (config.method && config.method.toUpperCase() !== 'GET') {
                config.headers = {
                    ...config.headers,
                    'X-CSRF-TOKEN': csrfToken
                };
            }
            return originalFetch(url, config);
        };
    </script>
</body>
</html>