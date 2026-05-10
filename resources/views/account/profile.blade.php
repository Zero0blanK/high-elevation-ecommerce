@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Sidebar (desktop) -->
        <div class="hidden lg:block lg:col-span-3">
            @include('account.partials.sidebar')
        </div>

        <div class="lg:col-span-9">
            <!-- Mobile tab navigation -->
            <div class="lg:hidden mb-6 flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('account.dashboard') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.dashboard') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('orders.*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('wishlist.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('wishlist.*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Wishlist</a>
                <a href="{{ route('account.addresses') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.addresses*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Addresses</a>
                <a href="{{ route('account.profile') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.profile*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Profile</a>
            </div>

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your personal information</p>
            </div>

            <!-- Profile Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{
                formErrors: {},
                isSubmitting: false,
                handleProfileSubmit() {
                    this.isSubmitting = true;
                    this.formErrors = {};
                    const form = event.target;
                    const submitBtn = form.querySelector('button[type=\'submit\']');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<svg class=\'animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Updating...';
                    submitBtn.disabled = true;
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid', 'border-red-500'));
                    form.querySelectorAll('.error-message').forEach(el => el.remove());
                    fetch(form.action, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content, 'Accept': 'application/json' },
                        body: JSON.stringify(Object.fromEntries(new FormData(form)))
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const el = document.createElement('div');
                            el.className = 'mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm';
                            el.textContent = 'Profile updated successfully!';
                            form.insertBefore(el, form.firstChild);
                            setTimeout(() => el.remove(), 3000);
                        } else {
                            this.formErrors = data.errors;
                            Object.keys(data.errors).forEach(field => {
                                const input = form.querySelector(`[name='${field}']`);
                                if (input) {
                                    input.classList.add('is-invalid', 'border-red-500');
                                    const err = document.createElement('p');
                                    err.className = 'mt-1 text-xs text-red-600 error-message';
                                    err.textContent = data.errors[field][0];
                                    input.parentNode.insertBefore(err, input.nextSibling);
                                }
                            });
                        }
                    })
                    .catch(() => {
                        const el = document.createElement('div');
                        el.className = 'mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm';
                        el.textContent = 'An error occurred. Please try again.';
                        form.insertBefore(el, form.firstChild);
                    })
                    .finally(() => { this.isSubmitting = false; submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; });
                }
            }">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                </div>
                <form @submit.prevent="handleProfileSubmit" action="{{ route('account.profile.update') }}" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" id="first_name" required minlength="2"
                                   pattern="[A-Za-z\s-]+" title="Letters, spaces, and hyphens only"
                                   value="{{ old('first_name', $customer->first_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   :class="{ 'border-red-500': formErrors.first_name }">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" id="last_name" required minlength="2"
                                   pattern="[A-Za-z\s-]+" title="Letters, spaces, and hyphens only"
                                   value="{{ old('last_name', $customer->last_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   :class="{ 'border-red-500': formErrors.last_name }">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" required
                               value="{{ old('email', $customer->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               :class="{ 'border-red-500': formErrors.email }">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" id="phone"
                                   title="International format (e.g., +639123456789)"
                                   value="{{ old('phone', $customer->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   :class="{ 'border-red-500': formErrors.phone }">
                            <p class="mt-1 text-xs text-gray-500">International format supported (e.g., +639123456789)</p>
                        </div>
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth"
                                   value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6" x-data="passwordChangeForm()">
                <button @click="showPassword = !showPassword" type="button"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
                    <svg :class="{ 'rotate-180': showPassword }" class="h-5 w-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="showPassword" x-cloak x-collapse>
                    <form @submit.prevent="handlePasswordSubmit" action="{{ route('account.password.update') }}" class="p-6 pt-0 space-y-5 border-t border-gray-200">
                        @csrf
                        @method('PATCH')

                        <div class="mt-5">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   :class="{ 'border-red-500': passwordFormErrors.current_password }">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" id="password" required minlength="8"
                                   x-model="newPassword"
                                   @input="checkPassword($event.target.value)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   :class="{ 'border-red-500': passwordFormErrors.password }">
                            <!-- Password Requirements Checklist -->
                            <div class="mt-2 space-y-1" x-show="newPassword.length > 0" x-cloak>
                                <div class="flex items-center gap-2 text-xs" :class="pwdChecks.length ? 'text-green-600' : 'text-gray-500'">
                                    <svg x-show="pwdChecks.length" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <svg x-show="!pwdChecks.length" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                    <span>At least 8 characters</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs" :class="pwdChecks.upper ? 'text-green-600' : 'text-gray-500'">
                                    <svg x-show="pwdChecks.upper" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <svg x-show="!pwdChecks.upper" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                    <span>1 uppercase letter (A-Z)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs" :class="pwdChecks.lower ? 'text-green-600' : 'text-gray-500'">
                                    <svg x-show="pwdChecks.lower" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <svg x-show="!pwdChecks.lower" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                    <span>1 lowercase letter (a-z)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs" :class="pwdChecks.number ? 'text-green-600' : 'text-gray-500'">
                                    <svg x-show="pwdChecks.number" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <svg x-show="!pwdChecks.number" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                    <span>1 number (0-9)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs" :class="pwdChecks.symbol ? 'text-green-600' : 'text-gray-500'">
                                    <svg x-show="pwdChecks.symbol" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <svg x-show="!pwdChecks.symbol" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                    <span>1 special character (!@#$%^&*)</span>
                                </div>
                            </div>
                            <p x-show="newPassword.length === 0" class="mt-1 text-xs text-gray-500">Min 8 chars: 1 uppercase, 1 lowercase, 1 number, 1 symbol</p>
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   :class="{ 'border-red-500': passwordFormErrors.password_confirmation }">
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function passwordChangeForm() {
    return {
        showPassword: false,
        passwordFormErrors: {},
        isPasswordSubmitting: false,
        newPassword: '',
        pwdChecks: { length: false, upper: false, lower: false, number: false, symbol: false },
        
        checkPassword(pwd) {
            this.newPassword = pwd;
            this.pwdChecks = {
                length: pwd.length >= 8,
                upper: /[A-Z]/.test(pwd),
                lower: /[a-z]/.test(pwd),
                number: /\d/.test(pwd),
                symbol: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(pwd)
            };
        },
        
        get passwordValid() {
            return this.pwdChecks.length && this.pwdChecks.upper && this.pwdChecks.lower && this.pwdChecks.number && this.pwdChecks.symbol;
        },
        
        handlePasswordSubmit() {
            this.isPasswordSubmitting = true;
            this.passwordFormErrors = {};
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Updating...';
            submitBtn.disabled = true;
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid', 'border-red-500'));
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            
            fetch(form.action, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            })
            .then(response => { 
                if (!response.ok) return response.json().then(d => Promise.reject(d)); 
                return response.json(); 
            })
            .then(data => {
                if (data.success) {
                    const el = document.createElement('div');
                    el.className = 'mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm';
                    el.textContent = data.message || 'Password updated successfully!';
                    form.insertBefore(el, form.firstChild);
                    form.reset();
                    this.newPassword = '';
                    this.pwdChecks = { length: false, upper: false, lower: false, number: false, symbol: false };
                    setTimeout(() => el.remove(), 3000);
                } else {
                    this.passwordFormErrors = data.errors || {};
                    this.showFieldErrors(form);
                    if (Object.keys(this.passwordFormErrors).length === 0) {
                        this.showGeneralError(form, data.message || 'Failed to update password.');
                    }
                }
            })
            .catch(error => {
                if (error && error.errors) {
                    this.passwordFormErrors = error.errors;
                    this.showFieldErrors(form);
                } else {
                    this.showGeneralError(form, error.message || 'An unexpected error occurred.');
                }
            })
            .finally(() => { 
                this.isPasswordSubmitting = false; 
                submitBtn.disabled = false; 
                submitBtn.innerHTML = originalBtnText; 
            });
        },
        
        showFieldErrors(form) {
            Object.keys(this.passwordFormErrors).forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid', 'border-red-500');
                    const err = document.createElement('p');
                    err.className = 'mt-1 text-xs text-red-600 error-message';
                    err.textContent = this.passwordFormErrors[field][0];
                    input.parentNode.insertBefore(err, input.nextSibling);
                }
            });
        },
        
        showGeneralError(form, message) {
            const el = document.createElement('div');
            el.className = 'mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm';
            el.textContent = message;
            form.insertBefore(el, form.firstChild);
        }
    };
}
</script>
@endpush
@endsection