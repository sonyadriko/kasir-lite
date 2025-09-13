@extends('layouts.admin')

@section('title', $title ?? 'Coming Soon')
@section('page-title', $title ?? 'Coming Soon')

@section('content')
<div class="min-h-96 flex items-center justify-center">
    <div class="text-center">
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
            </svg>
        </div>
        
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?? 'Coming Soon' }}</h2>
        
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            {{ $description ?? 'This feature is currently under development and will be available soon.' }}
        </p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 max-w-lg mx-auto">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Demo Mode</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        You are currently viewing a demo version. The full admin interface includes advanced features for sales reporting, user management, and system configuration.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7 4-4 4 4"></path>
                </svg>
                Back to Dashboard
            </a>
            
            <div class="text-sm text-gray-500">
                <p>Available features:</p>
                <div class="mt-2 space-x-4">
                    <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:underline">Product Management</a>
                    <a href="/pos" class="text-blue-600 hover:underline">POS System</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection