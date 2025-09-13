<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kasir Lite</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div x-data="{
        async checkAuth() {
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
            
            // Parse user data
            try {
                const userData = JSON.parse(user);
                
                // Authenticate with Laravel session
                const response = await fetch('/admin/authenticate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        token: token
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Authentication successful:', data.message);
                    
                    // Set the user data in sessionStorage for Laravel to access
                    sessionStorage.setItem('admin_user', JSON.stringify(userData));
                    
                    // Redirect to actual dashboard with user data
                    window.location.href = '/admin/dashboard';
                } else {
                    console.error('Failed to authenticate with Laravel session');
                    // Still try to redirect - the middleware will handle auto-login in dev mode
                    window.location.href = '/admin/dashboard';
                }
            } catch (error) {
                console.error('Authentication error:', error);
                // Try to redirect anyway - middleware will handle it
                window.location.href = '/admin/dashboard';
            }
        }
    }" x-init="checkAuth()">
        
        <!-- Loading spinner -->
        <div class="min-h-screen flex items-center justify-center">
            <div class="text-center">
                <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Checking authentication...</p>
            </div>
        </div>
    </div>
</body>
</html>