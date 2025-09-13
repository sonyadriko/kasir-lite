@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="settingsManager()">
    
    <!-- Settings Navigation -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <h2 class="text-xl font-semibold">Settings Management</h2>
            <p class="text-blue-100 text-sm mt-1">Configure your system, outlet, and business settings</p>
        </div>
        
        <nav class="flex space-x-8 px-6 py-4 border-b border-gray-200">
            <button @click="activeTab = 'outlet'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'outlet', 'text-gray-500 hover:text-gray-700': activeTab !== 'outlet'}"
                    class="pb-2 px-1 font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8h1m4 0h1"/>
                </svg>
                Outlet Information
            </button>
            
            <button @click="activeTab = 'receipt'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'receipt', 'text-gray-500 hover:text-gray-700': activeTab !== 'receipt'}"
                    class="pb-2 px-1 font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Receipt Settings
            </button>
            
            <button @click="activeTab = 'payment'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'payment', 'text-gray-500 hover:text-gray-700': activeTab !== 'payment'}"
                    class="pb-2 px-1 font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Payment & Tax
            </button>
            
            <button @click="activeTab = 'categories'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'categories', 'text-gray-500 hover:text-gray-700': activeTab !== 'categories'}"
                    class="pb-2 px-1 font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categories
            </button>
            
            <button @click="activeTab = 'system'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'system', 'text-gray-500 hover:text-gray-700': activeTab !== 'system'}"
                    class="pb-2 px-1 font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                System Tools
            </button>
        </nav>
    </div>
    
    <!-- System Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Categories</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_categories']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Sales</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_sales']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Database</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['database_size'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Storage</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['storage_size'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Sections -->
    <div class="bg-white rounded-lg shadow-sm border">
        
        <!-- Outlet Information Tab -->
        <div x-show="activeTab === 'outlet'" class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Outlet Information</h3>
                <p class="text-gray-600">Configure your business information and basic settings</p>
            </div>
            
            <form action="{{ route('admin.settings.outlet.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Outlet Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', $outlet->name ?? '') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email', $outlet->email ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" name="phone" id="phone" 
                               value="{{ old('phone', $outlet->phone ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="+62 21 1234 5678">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency <span class="text-red-500">*</span>
                        </label>
                        <select name="currency" id="currency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="IDR" {{ old('currency', $outlet->currency ?? 'IDR') === 'IDR' ? 'selected' : '' }}>Indonesian Rupiah (IDR)</option>
                            <option value="USD" {{ old('currency', $outlet->currency ?? '') === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                            <option value="MYR" {{ old('currency', $outlet->currency ?? '') === 'MYR' ? 'selected' : '' }}>Malaysian Ringgit (MYR)</option>
                            <option value="SGD" {{ old('currency', $outlet->currency ?? '') === 'SGD' ? 'selected' : '' }}>Singapore Dollar (SGD)</option>
                        </select>
                        @error('currency')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                              placeholder="Enter your business address">{{ old('address', $outlet->address ?? '') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              placeholder="Brief description of your business">{{ old('description', $outlet->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                        Timezone <span class="text-red-500">*</span>
                    </label>
                    <select name="timezone" id="timezone" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Asia/Jakarta" {{ old('timezone', $outlet->timezone ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                        <option value="Asia/Makassar" {{ old('timezone', $outlet->timezone ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                        <option value="Asia/Jayapura" {{ old('timezone', $outlet->timezone ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        <option value="Asia/Singapore" {{ old('timezone', $outlet->timezone ?? '') === 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (SGT)</option>
                        <option value="Asia/Kuala_Lumpur" {{ old('timezone', $outlet->timezone ?? '') === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Asia/Kuala_Lumpur (MYT)</option>
                    </select>
                    @error('timezone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Outlet Information
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Receipt Settings Tab -->
        <div x-show="activeTab === 'receipt'" class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Receipt Settings</h3>
                <p class="text-gray-600">Configure receipt templates and printing options</p>
            </div>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="receipt_header" class="block text-sm font-medium text-gray-700 mb-2">
                            Receipt Header Text
                        </label>
                        <textarea name="settings[receipt_header]" id="receipt_header" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Thank you for your purchase!">{{ old('settings.receipt_header', $receiptSettings['receipt_header'] ?? '') }}</textarea>
                    </div>
                    
                    <div>
                        <label for="receipt_footer" class="block text-sm font-medium text-gray-700 mb-2">
                            Receipt Footer Text
                        </label>
                        <textarea name="settings[receipt_footer]" id="receipt_footer" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Please come again!">{{ old('settings.receipt_footer', $receiptSettings['receipt_footer'] ?? '') }}</textarea>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="receipt_width" class="block text-sm font-medium text-gray-700 mb-2">
                            Receipt Width (characters)
                        </label>
                        <input type="number" name="settings[receipt_width]" id="receipt_width" 
                               value="{{ old('settings.receipt_width', $receiptSettings['receipt_width'] ?? '40') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="20" max="80">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Receipt Options
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="settings[auto_print]" value="1" 
                                       {{ old('settings.auto_print', $receiptSettings['auto_print'] ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Auto-print receipts</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="settings[show_barcode]" value="1"
                                       {{ old('settings.show_barcode', $receiptSettings['show_barcode'] ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Show product barcodes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="settings[show_logo]" value="1"
                                       {{ old('settings.show_logo', $receiptSettings['show_logo'] ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Show outlet logo</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Receipt Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Payment & Tax Tab -->
        <div x-show="activeTab === 'payment'" class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment & Tax Settings</h3>
                <p class="text-gray-600">Configure tax rates and payment method options</p>
            </div>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Tax Configuration</h4>
                    <p class="text-sm text-blue-700">Set up your tax rates for sales calculations</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Tax Rate (%)
                        </label>
                        <input type="number" name="settings[tax_rate]" id="tax_rate" step="0.01"
                               value="{{ old('settings.tax_rate', $generalSettings['tax_rate'] ?? '10') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="0" max="100">
                    </div>
                    
                    <div>
                        <label for="tax_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Name/Label
                        </label>
                        <input type="text" name="settings[tax_name]" id="tax_name"
                               value="{{ old('settings.tax_name', $generalSettings['tax_name'] ?? 'PPN') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="PPN, VAT, GST, etc.">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Application
                        </label>
                        <select name="settings[tax_inclusive]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="0" {{ old('settings.tax_inclusive', $generalSettings['tax_inclusive'] ?? '0') == '0' ? 'selected' : '' }}>Tax Exclusive</option>
                            <option value="1" {{ old('settings.tax_inclusive', $generalSettings['tax_inclusive'] ?? '0') == '1' ? 'selected' : '' }}>Tax Inclusive</option>
                        </select>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-green-800 mb-3">Payment Methods</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="settings[payment_cash]" value="1"
                                   {{ old('settings.payment_cash', $paymentSettings['payment_cash'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Cash</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="settings[payment_card]" value="1"
                                   {{ old('settings.payment_card', $paymentSettings['payment_card'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Card</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="settings[payment_transfer]" value="1"
                                   {{ old('settings.payment_transfer', $paymentSettings['payment_transfer'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Bank Transfer</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="settings[payment_ewallet]" value="1"
                                   {{ old('settings.payment_ewallet', $paymentSettings['payment_ewallet'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">E-Wallet</span>
                        </label>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Payment Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Categories Management Tab -->
        <div x-show="activeTab === 'categories'" class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Categories Management</h3>
                    <p class="text-gray-600">Manage product categories for better organization</p>
                </div>
                <button @click="showAddCategory = true" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Category
                </button>
            </div>
            
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categories as $category)
                    <div class="bg-gray-50 border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-900">{{ $category->name }}</h4>
                            <div class="flex space-x-2">
                                <button @click="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}')" 
                                        class="text-blue-600 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.settings.categories.delete', $category) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($category->description)
                            <p class="text-sm text-gray-600 mb-2">{{ $category->description }}</p>
                        @endif
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            {{ $category->products_count ?? 0 }} products
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first product category.</p>
                </div>
            @endif
        </div>
        
        <!-- System Tools Tab -->
        <div x-show="activeTab === 'system'" class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">System Tools</h3>
                <p class="text-gray-600">System maintenance and administrative tools</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Backup & Restore -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-blue-900 mb-3">Database Backup</h4>
                    <p class="text-sm text-blue-700 mb-4">Create a backup of your database for safekeeping.</p>
                    <form action="{{ route('admin.settings.backup') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-800">Last backup: Never</span>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                Create Backup
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Cache Management -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-green-900 mb-3">Cache Management</h4>
                    <p class="text-sm text-green-700 mb-4">Clear system cache to improve performance.</p>
                    <form action="{{ route('admin.settings.cache.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Clear All Cache
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- System Information -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">System Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">PHP Version:</span>
                            <span class="text-sm font-medium text-gray-900">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Laravel Version:</span>
                            <span class="text-sm font-medium text-gray-900">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Environment:</span>
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ app()->environment() }}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Memory Limit:</span>
                            <span class="text-sm font-medium text-gray-900">{{ ini_get('memory_limit') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Upload Max Size:</span>
                            <span class="text-sm font-medium text-gray-900">{{ ini_get('upload_max_filesize') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Max Execution Time:</span>
                            <span class="text-sm font-medium text-gray-900">{{ ini_get('max_execution_time') }}s</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Category Modal -->
    <div x-show="showAddCategory || showEditCategory" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="showEditCategory ? 'Edit Category' : 'Add New Category'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form :action="showEditCategory ? '{{ route('admin.settings.categories.update', '') }}/' + editingCategoryId : '{{ route('admin.settings.categories.create') }}'" method="POST">
                    @csrf
                    <input x-show="showEditCategory" type="hidden" name="_method" value="PUT">
                    
                    <div class="mb-4">
                        <label for="category_name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <input type="text" name="name" id="category_name" x-model="categoryForm.name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="mb-4">
                        <label for="category_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="category_description" x-model="categoryForm.description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <span x-text="showEditCategory ? 'Update' : 'Create'"></span> Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function settingsManager() {
        return {
            activeTab: 'outlet',
            showAddCategory: false,
            showEditCategory: false,
            editingCategoryId: null,
            categoryForm: {
                name: '',
                description: ''
            },
            
            editCategory(id, name, description) {
                this.editingCategoryId = id;
                this.categoryForm.name = name;
                this.categoryForm.description = description;
                this.showEditCategory = true;
            },
            
            closeModal() {
                this.showAddCategory = false;
                this.showEditCategory = false;
                this.editingCategoryId = null;
                this.categoryForm = { name: '', description: '' };
            }
        }
    }
</script>
@endpush

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush