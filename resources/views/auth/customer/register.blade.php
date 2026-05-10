@extends('layouts.app')

@section('title', 'Customer Registration')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('customer.login') }}" class="font-medium text-amber-600 hover:text-amber-500 transition-colors">
                    sign in to your existing account
                </a>
            </p>
        </div>

        {{-- Error Alert --}}
        <div id="register-error-alert" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg transition-all duration-300 opacity-0 transform -translate-y-2">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="register-error-message" class="text-sm"></span>
            </div>
        </div>

        {{-- Success Alert --}}
        <div id="register-success-alert" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg transition-all duration-300 opacity-0 transform -translate-y-2">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="register-success-message" class="text-sm">Registration successful! Redirecting...</span>
            </div>
        </div>
        
        <form id="register-form" class="mt-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input id="first_name" 
                               name="first_name" 
                               type="text" 
                               required 
                               value="{{ old('first_name') }}"
                               class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                               placeholder="John">
                        <p id="first_name-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span></span>
                        </p>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input id="last_name" 
                               name="last_name" 
                               type="text" 
                               required 
                               value="{{ old('last_name') }}"
                               class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                               placeholder="Doe">
                        <p id="last_name-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span></span>
                        </p>
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           value="{{ old('email') }}"
                           class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                           placeholder="you@example.com">
                    <p id="email-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span></span>
                    </p>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="phone" 
                           name="phone" 
                           type="tel" 
                           value="{{ old('phone') }}"
                           class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                           placeholder="+639123456789 or 09123456789">
                    <p class="mt-1 text-xs text-gray-500">International format supported (e.g., +1234567890)</p>
                    <p id="phone-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span></span>
                    </p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                           placeholder="••••••••">
                    <p class="mt-1 text-xs text-gray-500">Min 8 chars: 1 uppercase, 1 lowercase, 1 number, 1 symbol</p>
                    <p id="password-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span></span>
                    </p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                           placeholder="••••••••">
                    <p id="password_confirmation-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span></span>
                    </p>
                </div>
                
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="date_of_birth" 
                           name="date_of_birth" 
                           type="date" 
                           value="{{ old('date_of_birth') }}"
                           class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm">
                    <p id="date_of_birth-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span></span>
                    </p>
                </div>
                
                <div class="flex items-start">
                    <input id="marketing_emails" 
                           name="marketing_emails" 
                           type="checkbox" 
                           value="1"
                           {{ old('marketing_emails') ? 'checked' : '' }}
                           class="h-4 w-4 mt-0.5 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="marketing_emails" class="ml-2 block text-sm text-gray-700">
                        I would like to receive marketing emails about new products and offers
                    </label>
                </div>
            </div>

            <div>
                <button type="submit"
                        id="register-submit-btn" 
                        class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="register-btn-text">Create Account</span>
                    <svg id="register-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>

            <p class="text-center text-xs text-gray-500">
                By creating an account, you agree to our 
                <a href="#" class="text-amber-600 hover:text-amber-500">Terms of Service</a> and 
                <a href="#" class="text-amber-600 hover:text-amber-500">Privacy Policy</a>
            </p>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const submitBtn = document.getElementById('register-submit-btn');
    const btnText = document.getElementById('register-btn-text');
    const spinner = document.getElementById('register-spinner');
    const errorAlert = document.getElementById('register-error-alert');
    const errorMessage = document.getElementById('register-error-message');
    const successAlert = document.getElementById('register-success-alert');

    function showAlert(alertEl, message) {
        alertEl.classList.remove('hidden');
        setTimeout(() => {
            alertEl.classList.remove('opacity-0', '-translate-y-2');
        }, 10);
        if (message && alertEl.querySelector('span')) {
            alertEl.querySelector('span').textContent = message;
        }
    }

    function hideAlert(alertEl) {
        alertEl.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => {
            alertEl.classList.add('hidden');
        }, 300);
    }

    function clearFieldErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            if (el.id !== 'register-error-alert') {
                el.classList.add('hidden');
                const span = el.querySelector('span');
                if (span) span.textContent = '';
            }
        });
        document.querySelectorAll('#register-form input').forEach(el => {
            el.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            el.classList.add('border-gray-300', 'focus:ring-amber-500', 'focus:border-amber-500');
        });
    }

    function showFieldError(fieldId, message) {
        const errorEl = document.getElementById(fieldId + '-error');
        const inputEl = document.getElementById(fieldId);
        
        if (errorEl) {
            const span = errorEl.querySelector('span');
            if (span) span.textContent = message;
            errorEl.classList.remove('hidden');
        }
        if (inputEl) {
            inputEl.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            inputEl.classList.remove('border-gray-300', 'focus:ring-amber-500', 'focus:border-amber-500');
        }
    }

    function setLoading(isLoading) {
        submitBtn.disabled = isLoading;
        if (isLoading) {
            btnText.textContent = 'Creating account...';
            spinner.classList.remove('hidden');
        } else {
            btnText.textContent = 'Create Account';
            spinner.classList.add('hidden');
        }
    }

    // Clear field error when user starts typing
    document.querySelectorAll('#register-form input').forEach(input => {
        input.addEventListener('input', function() {
            const errorEl = document.getElementById(this.id + '-error');
            if (errorEl && !errorEl.classList.contains('hidden')) {
                errorEl.classList.add('hidden');
                this.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.add('border-gray-300', 'focus:ring-amber-500', 'focus:border-amber-500');
            }
            // Hide main error alert when user starts typing
            if (!errorAlert.classList.contains('hidden')) {
                hideAlert(errorAlert);
            }
        });
    });

    // Real-time password confirmation validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    
    confirmInput.addEventListener('input', function() {
        if (this.value && passwordInput.value && this.value !== passwordInput.value) {
            showFieldError('password_confirmation', 'Passwords do not match');
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Client-side password confirmation check
        if (passwordInput.value !== confirmInput.value) {
            showFieldError('password_confirmation', 'Passwords do not match');
            showAlert(errorAlert, 'Please fix the errors below.');
            confirmInput.focus();
            return;
        }
        
        // Clear previous errors
        hideAlert(errorAlert);
        hideAlert(successAlert);
        clearFieldErrors();
        
        // Show loading state
        setLoading(true);
        
        const formData = new FormData(form);
        
        fetch('{{ route("customer.register") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            return response.json().then(data => ({ status: response.status, data }));
        })
        .then(result => {
            if (result.data.success && result.data.redirect) {
                // Success - show success message then redirect
                showAlert(successAlert, 'Registration successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = result.data.redirect;
                }, 1000);
            } else if (result.status === 422 && result.data.errors) {
                // Validation errors - display them
                Object.keys(result.data.errors).forEach(field => {
                    showFieldError(field, result.data.errors[field][0]);
                });
                
                // Show general error alert
                showAlert(errorAlert, result.data.message || 'Please fix the errors below.');
                
                // Focus first error field
                const firstErrorField = Object.keys(result.data.errors)[0];
                const firstInput = document.getElementById(firstErrorField);
                if (firstInput) {
                    firstInput.focus();
                }
                
                setLoading(false);
            } else if (result.data.errors) {
                showAlert(errorAlert, result.data.errors.general?.[0] || 'An error occurred.');
                setLoading(false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert(errorAlert, 'An error occurred. Please try again.');
            setLoading(false);
        });
    });
});
</script>
@endpush
@endsection