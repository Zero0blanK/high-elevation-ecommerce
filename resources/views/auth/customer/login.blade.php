@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Customer account?
                <a href="{{ route('customer.register') }}" class="font-medium text-amber-600 hover:text-amber-500">
                    create a new account
                </a>
            </p>
        </div>

        {{-- Error Alert --}}
        <div id="login-error-alert" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg transition-all duration-300 opacity-0 transform -translate-y-2">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="login-error-message" class="text-sm"></span>
            </div>
        </div>
        
        <form id="login-form" class="mt-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
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
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors sm:text-sm" 
                           placeholder="••••••••">
                    <p id="password-error" class="hidden mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('customer.password.request') }}" class="font-medium text-amber-600 hover:text-amber-500 transition-colors">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        id="login-submit-btn"
                        class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="login-btn-text">Sign in</span>
                    <svg id="login-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const submitBtn = document.getElementById('login-submit-btn');
    const btnText = document.getElementById('login-btn-text');
    const spinner = document.getElementById('login-spinner');
    const errorAlert = document.getElementById('login-error-alert');
    const errorMessage = document.getElementById('login-error-message');

    function showError(alertEl, message) {
        alertEl.classList.remove('hidden');
        setTimeout(() => {
            alertEl.classList.remove('opacity-0', '-translate-y-2');
        }, 10);
        if (message) {
            errorMessage.textContent = message;
        }
    }

    function hideError(alertEl) {
        alertEl.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => {
            alertEl.classList.add('hidden');
        }, 300);
    }

    function clearFieldErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            if (el.id !== 'login-error-alert') {
                el.classList.add('hidden');
                const span = el.querySelector('span');
                if (span) span.textContent = '';
            }
        });
        document.querySelectorAll('input').forEach(el => {
            el.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
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
            btnText.textContent = 'Signing in...';
            spinner.classList.remove('hidden');
        } else {
            btnText.textContent = 'Sign in';
            spinner.classList.add('hidden');
        }
    }

    // Clear errors when user starts typing
    document.querySelectorAll('#login-form input').forEach(input => {
        input.addEventListener('input', function() {
            const errorEl = document.getElementById(this.id + '-error');
            if (errorEl && !errorEl.classList.contains('hidden')) {
                errorEl.classList.add('hidden');
                this.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.add('border-gray-300', 'focus:ring-amber-500', 'focus:border-amber-500');
            }
            // Hide main error alert when user starts typing
            if (!errorAlert.classList.contains('hidden')) {
                hideError(errorAlert);
            }
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        hideError(errorAlert);
        clearFieldErrors();
        
        // Show loading state
        setLoading(true);
        
        const formData = new FormData(form);
        
        fetch('{{ route("customer.login") }}', {
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
                // Success - redirect
                window.location.href = result.data.redirect;
            } else if (result.status === 422 && result.data.errors) {
                // Validation errors - display them
                Object.keys(result.data.errors).forEach(field => {
                    showFieldError(field, result.data.errors[field][0]);
                });
                
                // Show general error alert
                showError(errorAlert, result.data.message || 'Please fix the errors below.');
                
                // Focus first error field
                const firstErrorField = Object.keys(result.data.errors)[0];
                const firstInput = document.getElementById(firstErrorField);
                if (firstInput) {
                    firstInput.focus();
                }
            } else {
                showError(errorAlert, result.data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(errorAlert, 'An error occurred. Please try again.');
        })
        .finally(() => {
            setLoading(false);
        });
    });
});
</script>
@endpush
@endsection
