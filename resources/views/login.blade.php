<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Lite - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Kasir Lite</h1>
            <p class="text-gray-600">Silakan login untuk melanjutkan</p>
        </div>
        
        <!-- Error Messages -->
        <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <span id="error-text"></span>
        </div>
        
        <!-- Success Message -->
        <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Login berhasil! Mengalihkan ke POS...
        </div>
        
        <form id="login-form" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="cashier@demo.local"
                >
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="password"
                >
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Ingat saya (7 hari)
                    </label>
                </div>
            </div>
            
            <button 
                type="submit" 
                id="login-btn"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center"
            >
                <span id="login-text">Masuk ke POS</span>
                <div id="login-spinner" class="hidden ml-2">
                    <div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                </div>
            </button>
        </form>
        
        <!-- Demo Credentials -->
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Demo Credentials:</h3>
            <div class="space-y-1 text-xs text-gray-600">
                <p><strong>Owner:</strong> owner@demo.local / password</p>
                <p><strong>Cashier:</strong> cashier@demo.local / password</p>
            </div>
            <div class="mt-2 space-x-2">
                <button onclick="fillDemo('cashier')" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                    Use Cashier
                </button>
                <button onclick="fillDemo('owner')" class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded hover:bg-purple-200">
                    Use Owner
                </button>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Demo credential filler
        function fillDemo(type) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            
            if (type === 'cashier') {
                email.value = 'cashier@demo.local';
                password.value = 'password';
            } else {
                email.value = 'owner@demo.local';
                password.value = 'password';
            }
        }
        
        // Error/Success message handlers
        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            errorText.textContent = message;
            errorDiv.classList.remove('hidden');
            
            // Hide success message if shown
            document.getElementById('success-message').classList.add('hidden');
        }
        
        function showSuccess() {
            const successDiv = document.getElementById('success-message');
            successDiv.classList.remove('hidden');
            
            // Hide error message if shown
            document.getElementById('error-message').classList.add('hidden');
        }
        
        function setLoading(isLoading) {
            const btn = document.getElementById('login-btn');
            const text = document.getElementById('login-text');
            const spinner = document.getElementById('login-spinner');
            
            if (isLoading) {
                btn.disabled = true;
                btn.classList.add('cursor-not-allowed', 'opacity-75');
                text.textContent = 'Logging in...';
                spinner.classList.remove('hidden');
            } else {
                btn.disabled = false;
                btn.classList.remove('cursor-not-allowed', 'opacity-75');
                text.textContent = 'Masuk ke POS';
                spinner.classList.add('hidden');
            }
        }
        
        // Login form handler
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            
            setLoading(true);
            
            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        remember: remember
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Store token and user info
                    const expiryDays = remember ? 7 : 1;
                    const expiryDate = new Date();
                    expiryDate.setTime(expiryDate.getTime() + (expiryDays * 24 * 60 * 60 * 1000));
                    
                    // Store in localStorage for session management
                    localStorage.setItem('pos_token', data.token);
                    localStorage.setItem('pos_user', JSON.stringify(data.user));
                    localStorage.setItem('pos_token_expires', expiryDate.getTime());
                    
                    showSuccess();
                    
                    // Redirect to POS after short delay
                    setTimeout(() => {
                        window.location.href = '/pos';
                    }, 1500);
                } else {
                    showError(data.message || 'Login gagal. Periksa email dan password.');
                }
            } catch (error) {
                console.error('Login error:', error);
                showError('Terjadi kesalahan. Silakan coba lagi.');
            }
            
            setLoading(false);
        });
        
        // Auto-focus email field
        document.getElementById('email').focus();
    </script>
</body>
</html>