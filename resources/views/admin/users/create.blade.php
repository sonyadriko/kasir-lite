@extends('layouts.admin')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Create New Staff Account</h3>
            <p class="mt-1 text-sm text-gray-600">Add a new team member to your outlet</p>
        </div>
        
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6" x-data="{
                password: '',
                passwordConfirmation: '',
                passwordsMatch: false,
                showPasswordStrength: false,
                checkPasswordMatch() {
                    this.passwordsMatch = this.password === this.passwordConfirmation && this.password.length >= 6;
                },
                getPasswordStrength() {
                    const password = this.password;
                    if (password.length < 6) return { score: 0, label: 'Too short', color: 'red' };
                    
                    let score = 0;
                    if (password.length >= 8) score += 1;
                    if (/[A-Z]/.test(password)) score += 1;
                    if (/[0-9]/.test(password)) score += 1;
                    if (/[^A-Za-z0-9]/.test(password)) score += 1;
                    
                    const levels = [
                        { score: 1, label: 'Weak', color: 'red' },
                        { score: 2, label: 'Fair', color: 'yellow' },
                        { score: 3, label: 'Good', color: 'blue' },
                        { score: 4, label: 'Strong', color: 'green' }
                    ];
                    
                    return levels.find(level => score <= level.score) || levels[levels.length - 1];
                }
            }">
            @csrf
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                               placeholder="+62 812 3456 7890">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" 
                                id="role" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror"
                                required>
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator - Full system access</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff - Limited access for daily operations</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Security -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Security</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               x-model="password"
                               @input="checkPasswordMatch(); showPasswordStrength = password.length > 0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors @error('password') border-red-500 @enderror"
                               placeholder="Enter secure password"
                               required>
                        
                        <!-- Password Strength Indicator -->
                        <div x-show="showPasswordStrength && password.length > 0" class="mt-2">
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1">
                                    <div class="h-1 w-6 rounded" :class="{
                                        'bg-red-500': getPasswordStrength().color === 'red',
                                        'bg-yellow-500': getPasswordStrength().color === 'yellow',
                                        'bg-blue-500': getPasswordStrength().color === 'blue',
                                        'bg-green-500': getPasswordStrength().color === 'green'
                                    }"></div>
                                    <div class="h-1 w-6 rounded" :class="{
                                        'bg-yellow-500': ['yellow', 'blue', 'green'].includes(getPasswordStrength().color),
                                        'bg-gray-200': !['yellow', 'blue', 'green'].includes(getPasswordStrength().color)
                                    }"></div>
                                    <div class="h-1 w-6 rounded" :class="{
                                        'bg-blue-500': ['blue', 'green'].includes(getPasswordStrength().color),
                                        'bg-gray-200': !['blue', 'green'].includes(getPasswordStrength().color)
                                    }"></div>
                                    <div class="h-1 w-6 rounded" :class="{
                                        'bg-green-500': getPasswordStrength().color === 'green',
                                        'bg-gray-200': getPasswordStrength().color !== 'green'
                                    }"></div>
                                </div>
                                <span class="text-xs font-medium" :class="{
                                    'text-red-600': getPasswordStrength().color === 'red',
                                    'text-yellow-600': getPasswordStrength().color === 'yellow',
                                    'text-blue-600': getPasswordStrength().color === 'blue',
                                    'text-green-600': getPasswordStrength().color === 'green'
                                }" x-text="getPasswordStrength().label"></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-500">Minimum 6 characters required</p>
                            <div class="relative" x-data="{ showTip: false }">
                                <button type="button" 
                                        @mouseenter="showTip = true" 
                                        @mouseleave="showTip = false"
                                        class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <div x-show="showTip" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg z-10 w-48">
                                    <div class="space-y-1">
                                        <p class="font-medium">Strong passwords include:</p>
                                        <ul class="list-disc list-inside space-y-0.5 text-gray-300">
                                            <li>At least 8 characters</li>
                                            <li>Uppercase letters (A-Z)</li>
                                            <li>Numbers (0-9)</li>
                                            <li>Special characters (!@#$)</li>
                                        </ul>
                                    </div>
                                    <div class="absolute top-full right-4 -mt-1">
                                        <div class="w-2 h-2 bg-gray-900 transform rotate-45"></div>
                                    </div>
                                </div>
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
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   x-model="passwordConfirmation"
                                   @input="checkPasswordMatch()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 transition-colors"
                                   :class="{
                                       'border-green-300 focus:ring-green-500': passwordsMatch && password.length >= 6 && passwordConfirmation.length >= 6,
                                       'border-red-300 focus:ring-red-500': !passwordsMatch && passwordConfirmation.length > 0,
                                       'focus:ring-blue-500': passwordConfirmation.length === 0
                                   }"
                                   placeholder="Confirm your password"
                                   required>
                            
                            <!-- Match/No Match Icon -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="passwordConfirmation.length > 0">
                                <svg x-show="passwordsMatch && password.length >= 6" class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <svg x-show="!passwordsMatch || password.length < 6" class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Password Match Status -->
                        <p class="text-xs mt-1 transition-colors" 
                           x-show="passwordConfirmation.length > 0"
                           :class="{
                               'text-green-600': passwordsMatch && password.length >= 6,
                               'text-red-600': !passwordsMatch || password.length < 6
                           }"
                           x-text="passwordsMatch && password.length >= 6 ? '✓ Passwords match' : '✗ Passwords do not match'"></p>
                        
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Role Description -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Role Permissions</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <div class="role-description" data-role="admin" style="display: none;">
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <strong>Administrator:</strong> Complete system access including user management, system settings, sales reports, inventory management, and all administrative functions.
                            </div>
                        </div>
                    </div>
                    <div class="role-description" data-role="staff" style="display: none;">
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <strong>Staff:</strong> Access to POS system, process sales transactions, view basic sales reports, and manage inventory. Limited administrative access.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="password.length < 6 || !passwordsMatch || passwordConfirmation.length === 0"
                        :class="{
                            'opacity-50 cursor-not-allowed': password.length < 6 || !passwordsMatch || passwordConfirmation.length === 0,
                            'hover:bg-blue-700': password.length >= 6 && passwordsMatch && passwordConfirmation.length > 0
                        }"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const roleDescriptions = document.querySelectorAll('.role-description');
        
        function showRoleDescription() {
            // Hide all descriptions
            roleDescriptions.forEach(desc => desc.style.display = 'none');
            
            // Show selected role description
            const selectedRole = roleSelect.value;
            if (selectedRole) {
                const selectedDesc = document.querySelector(`.role-description[data-role="${selectedRole}"]`);
                if (selectedDesc) {
                    selectedDesc.style.display = 'block';
                }
            }
        }
        
        roleSelect.addEventListener('change', showRoleDescription);
        
        // Show initial description if role is pre-selected
        showRoleDescription();
    });
</script>
@endpush
