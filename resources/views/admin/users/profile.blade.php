@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="profileManager()">
    
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-8 text-white">
            <div class="flex items-center space-x-6">
                <!-- Profile Avatar -->
                <div class="relative">
                    <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-3xl font-bold text-white backdrop-blur-sm border-4 border-white/30">
                        @if(auth()->check() && auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Profile" class="w-full h-full rounded-full object-cover">
                        @elseif(auth()->check())
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @else
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <button @click="showAvatarModal = true" class="absolute bottom-0 right-0 bg-blue-500 hover:bg-blue-600 text-white rounded-full p-2 shadow-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Profile Info -->
                <div class="flex-1">
                    <h1 class="text-2xl font-bold mb-2">{{ auth()->check() ? auth()->user()->name : 'Guest User' }}</h1>
                    @if(auth()->check())
                    <div class="space-y-1 text-blue-100">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            {{ auth()->user()->email }}
                        </p>
                        @if(auth()->user()->phone)
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            {{ auth()->user()->phone }}
                        </p>
                        @endif
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="capitalize font-medium">{{ auth()->user()->role }}</span>
                        </p>
                    </div>
                    @endif
                </div>
                
                <!-- Quick Actions -->
                <div class="flex space-x-2">
                    <button @click="activeTab = 'personal'" 
                            class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition-colors backdrop-blur-sm">
                        Edit Profile
                    </button>
                    <button @click="activeTab = 'security'" 
                            class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition-colors backdrop-blur-sm">
                        Security
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Navigation Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <nav class="space-y-1 p-4">
                    <button @click="activeTab = 'personal'" 
                            :class="{'bg-blue-50 text-blue-700 border-r-2 border-blue-500': activeTab === 'personal', 'text-gray-600 hover:bg-gray-50': activeTab !== 'personal'}"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </button>
                    
                    <button @click="activeTab = 'security'" 
                            :class="{'bg-blue-50 text-blue-700 border-r-2 border-blue-500': activeTab === 'security', 'text-gray-600 hover:bg-gray-50': activeTab !== 'security'}"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Security Settings
                    </button>
                    
                    <button @click="activeTab = 'activity'" 
                            :class="{'bg-blue-50 text-blue-700 border-r-2 border-blue-500': activeTab === 'activity', 'text-gray-600 hover:bg-gray-50': activeTab !== 'activity'}"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activity Log
                    </button>
                    
                    <button @click="activeTab = 'preferences'" 
                            :class="{'bg-blue-50 text-blue-700 border-r-2 border-blue-500': activeTab === 'preferences', 'text-gray-600 hover:bg-gray-50': activeTab !== 'preferences'}"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Preferences
                    </button>
                </nav>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="lg:col-span-3">
            
            <!-- Personal Information Tab -->
            <div x-show="activeTab === 'personal'" class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                    <p class="text-sm text-gray-600">Update your personal details and contact information</p>
                </div>
                
                <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('phone') border-red-500 @enderror"
                                placeholder="+62 812 3456 7890">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role
                            </label>
                            <input type="text" id="role" value="{{ ucfirst(auth()->user()->role) }}" disabled
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                            <p class="text-xs text-gray-500 mt-1">Role cannot be changed from profile. Contact administrator.</p>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="resetForm()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                Reset Changes
                            </button>
                            <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Security Settings Tab -->
            <div x-show="activeTab === 'security'" class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
                    <p class="text-sm text-gray-600">Manage your password and security preferences</p>
                </div>
                
                <!-- Change Password Form -->
                <form action="{{ route('admin.profile.change-password') }}" method="POST" class="p-6 space-y-6" x-data="passwordChanger()">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Password Security</h4>
                                <p class="text-sm text-yellow-700 mt-1">Use a strong password with at least 8 characters, including uppercase letters, numbers, and special characters.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" required
                                    x-model="currentPassword"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('current_password') border-red-500 @enderror">
                                <button type="button" @click="showCurrentPassword = !showCurrentPassword"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showCurrentPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showCurrentPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m6-6l6 6-6 6-6-6z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" required
                                    x-model="newPassword" @input="checkPasswordStrength(); checkPasswordMatch()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('password') border-red-500 @enderror">
                                
                                <!-- Password Strength Indicator -->
                                <div x-show="newPassword.length > 0" class="mt-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex space-x-1">
                                            <div class="h-1 w-6 rounded" :class="passwordStrength.color"></div>
                                            <div class="h-1 w-6 rounded" :class="passwordStrength.score >= 2 ? passwordStrength.color : 'bg-gray-200'"></div>
                                            <div class="h-1 w-6 rounded" :class="passwordStrength.score >= 3 ? passwordStrength.color : 'bg-gray-200'"></div>
                                            <div class="h-1 w-6 rounded" :class="passwordStrength.score >= 4 ? passwordStrength.color : 'bg-gray-200'"></div>
                                        </div>
                                        <span class="text-xs font-medium" :class="'text-' + passwordStrength.color.split('-')[1] + '-600'" x-text="passwordStrength.label"></span>
                                    </div>
                                </div>
                                
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm New Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        x-model="passwordConfirmation" @input="checkPasswordMatch()"
                                        :class="passwordMatch ? 'border-green-300 focus:ring-green-500' : (passwordConfirmation.length > 0 ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500')"
                                        class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                                    
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="passwordConfirmation.length > 0">
                                        <svg x-show="passwordMatch" class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <svg x-show="!passwordMatch" class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                
                                <p class="text-xs mt-1 transition-colors" 
                                   x-show="passwordConfirmation.length > 0"
                                   :class="passwordMatch ? 'text-green-600' : 'text-red-600'"
                                   x-text="passwordMatch ? '✓ Passwords match' : '✗ Passwords do not match'"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="resetPasswordForm()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                :disabled="!passwordMatch || passwordStrength.score < 2 || currentPassword.length === 0"
                                :class="{'opacity-50 cursor-not-allowed': !passwordMatch || passwordStrength.score < 2 || currentPassword.length === 0}"
                                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Activity Log Tab -->
            <div x-show="activeTab === 'activity'" class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Activity Log</h3>
                    <p class="text-sm text-gray-600">Recent account activities and login history</p>
                </div>
                
                <div class="p-6">
                    <!-- Activity Timeline -->
                    <div class="flow-root">
                        @if($activities->count() > 0)
                            <ul class="-mb-8">
                                @foreach($activities as $activity)
                                    <li>
                                        <div class="relative {{ !$loop->last ? 'pb-8' : '' }}">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <!-- Activity Icon -->
                                                <div class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                    @switch($activity->activity_type)
                                                        @case('login')
                                                            bg-green-500
                                                            @break
                                                        @case('profile_update')
                                                            bg-blue-500
                                                            @break
                                                        @case('password_change')
                                                            bg-orange-500
                                                            @break
                                                        @case('avatar_update')
                                                            bg-purple-500
                                                            @break
                                                        @default
                                                            bg-gray-400
                                                    @endswitch">
                                                    
                                                    @switch($activity->activity_type)
                                                        @case('login')
                                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            @break
                                                        @case('profile_update')
                                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                                                            </svg>
                                                            @break
                                                        @case('password_change')
                                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            @break
                                                        @case('avatar_update')
                                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                            </svg>
                                                            @break
                                                        @default
                                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                            </svg>
                                                    @endswitch
                                                </div>
                                                
                                                <!-- Activity Content -->
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">{{ $activity->description }}</p>
                                                        <p class="text-xs text-gray-400">
                                                            IP: {{ $activity->ip_address ?? 'Unknown' }} • 
                                                            @if($activity->user_agent)
                                                                @php
                                                                    // Simple browser detection
                                                                    $browser = 'Unknown Browser';
                                                                    if (strpos($activity->user_agent, 'Chrome') !== false) $browser = 'Chrome';
                                                                    elseif (strpos($activity->user_agent, 'Firefox') !== false) $browser = 'Firefox';
                                                                    elseif (strpos($activity->user_agent, 'Safari') !== false) $browser = 'Safari';
                                                                    elseif (strpos($activity->user_agent, 'Edge') !== false) $browser = 'Edge';
                                                                @endphp
                                                                {{ $browser }}
                                                            @else
                                                                Unknown Browser
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time title="{{ $activity->created_at->format('F j, Y \a\t g:i A') }}">
                                                            {{ $activity->created_at->format('M d, H:i') }}
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <!-- No activities yet -->
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No activity yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Your account activities will appear here.</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 text-center">
                        <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                            View All Activity
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Preferences Tab -->
            <div x-show="activeTab === 'preferences'" class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Preferences</h3>
                    <p class="text-sm text-gray-600">Customize your account settings and notifications</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Notification Settings -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Notifications</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Email Notifications</label>
                                    <p class="text-xs text-gray-500">Receive notifications about sales and system updates</p>
                                </div>
                                <button type="button" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-blue-600 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <span class="translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">SMS Notifications</label>
                                    <p class="text-xs text-gray-500">Get SMS alerts for important events</p>
                                </div>
                                <button type="button" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Language & Region -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Language & Region</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                                <select id="language" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="en">English</option>
                                    <option value="id" selected>Bahasa Indonesia</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                                <select id="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Privacy Settings -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Privacy</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="show_online_status" checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="show_online_status" class="ml-2 text-sm text-gray-700">Show online status to other users</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="allow_analytics"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="allow_analytics" class="ml-2 text-sm text-gray-700">Allow usage analytics for system improvement</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                Save Preferences
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avatar Upload Modal -->
    <div x-show="showAvatarModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Update Profile Photo</h3>
                    <button @click="showAvatarModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="text-center mb-4">
                        <div class="w-24 h-24 mx-auto bg-gray-200 rounded-full flex items-center justify-center text-2xl font-bold text-gray-500">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Profile" class="w-full h-full rounded-full object-cover">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">Choose New Photo</label>
                        <input type="file" name="avatar" id="avatar" accept="image/*" required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF (Max 2MB)</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAvatarModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            Upload Photo
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
    function profileManager() {
        return {
            activeTab: 'personal',
            showAvatarModal: false,
            showCurrentPassword: false,
            
            resetForm() {
                // Reset form to original values
                document.querySelector('form').reset();
            }
        }
    }
    
    function passwordChanger() {
        return {
            currentPassword: '',
            newPassword: '',
            passwordConfirmation: '',
            passwordMatch: false,
            passwordStrength: { score: 0, label: 'Too short', color: 'bg-red-500' },
            
            checkPasswordMatch() {
                this.passwordMatch = this.newPassword === this.passwordConfirmation && this.newPassword.length >= 8;
            },
            
            checkPasswordStrength() {
                const password = this.newPassword;
                if (password.length < 6) {
                    this.passwordStrength = { score: 0, label: 'Too short', color: 'bg-red-500' };
                    return;
                }
                
                let score = 0;
                if (password.length >= 8) score += 1;
                if (/[A-Z]/.test(password)) score += 1;
                if (/[0-9]/.test(password)) score += 1;
                if (/[^A-Za-z0-9]/.test(password)) score += 1;
                
                const levels = [
                    { score: 1, label: 'Weak', color: 'bg-red-500' },
                    { score: 2, label: 'Fair', color: 'bg-yellow-500' },
                    { score: 3, label: 'Good', color: 'bg-blue-500' },
                    { score: 4, label: 'Strong', color: 'bg-green-500' }
                ];
                
                this.passwordStrength = levels.find(level => score <= level.score) || levels[levels.length - 1];
                this.passwordStrength.score = score;
            },
            
            resetPasswordForm() {
                this.currentPassword = '';
                this.newPassword = '';
                this.passwordConfirmation = '';
                this.passwordMatch = false;
                this.passwordStrength = { score: 0, label: 'Too short', color: 'bg-red-500' };
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