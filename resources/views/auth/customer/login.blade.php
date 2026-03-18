@extends('layouts.app')

@section('title', 'Customer Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('customer.register') }}" class="font-medium text-amber-600 hover:text-amber-500">
                    create a new account
                </a>
            </p>
        </div>

        {{-- Error Alert --}}
        <div id="login-error-alert" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="login-error-message"></span>
            </div>
        </div>
        
        <form id="login-form" class="mt-8 space-y-6">
            @csrf
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           value="{{ old('email') }}"
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address">
                    <p id="email-error" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                    <p id="password-error" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('customer.password.request') }}" class="font-medium text-amber-600 hover:text-amber-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        id="login-submit-btn"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
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
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = document.getElementById('login-submit-btn');
    const btnText = document.getElementById('login-btn-text');
    const spinner = document.getElementById('login-spinner');
    const errorAlert = document.getElementById('login-error-alert');
    const errorMessage = document.getElementById('login-error-message');
    
    // Clear previous errors
    errorAlert.classList.add('hidden');
    document.querySelectorAll('[id$="-error"]').forEach(el => {
        if (el.id !== 'login-error-alert') {
            el.classList.add('hidden');
            el.textContent = '';
        }
    });
    document.querySelectorAll('input').forEach(el => {
        el.classList.remove('border-red-500');
    });
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.textContent = 'Signing in...';
    spinner.classList.remove('hidden');
    
    const formData = new FormData(form);
    
    fetch('{{ route("customer.login") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        return response.json().then(data => ({ status: response.status, data }));
    })
    .then(result => {
        if (!result) return; // Redirected
        
        if (result.status === 422 && result.data.errors) {
            // Display validation errors
            Object.keys(result.data.errors).forEach(field => {
                const errorEl = document.getElementById(field + '-error');
                const inputEl = document.getElementById(field);
                if (errorEl) {
                    errorEl.textContent = result.data.errors[field][0];
                    errorEl.classList.remove('hidden');
                }
                if (inputEl) {
                    inputEl.classList.add('border-red-500');
                }
            });
            
            // Show general error alert
            errorAlert.classList.remove('hidden');
            errorMessage.textContent = result.data.message || 'Please fix the errors below.';
        } else if (result.data.redirect) {
            window.location.href = result.data.redirect;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorAlert.classList.remove('hidden');
        errorMessage.textContent = 'An error occurred. Please try again.';
    })
    .finally(() => {
        submitBtn.disabled = false;
        btnText.textContent = 'Sign in';
        spinner.classList.add('hidden');
    });
});
</script>
@endpush
@endsection