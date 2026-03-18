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
                <a href="{{ route('customer.login') }}" class="font-medium text-amber-600 hover:text-amber-500">
                    sign in to your existing account
                </a>
            </p>
        </div>

        {{-- Error Alert --}}
        <div id="register-error-alert" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="register-error-message"></span>
            </div>
        </div>
        
        <form id="register-form" class="mt-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input id="first_name" 
                               name="first_name" 
                               type="text" 
                               required 
                               value="{{ old('first_name') }}"
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                               placeholder="First Name">
                        <p id="first_name-error" class="hidden mt-1 text-sm text-red-600"></p>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input id="last_name" 
                               name="last_name" 
                               type="text" 
                               required 
                               value="{{ old('last_name') }}"
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                               placeholder="Last Name">
                        <p id="last_name-error" class="hidden mt-1 text-sm text-red-600"></p>
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           value="{{ old('email') }}"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                           placeholder="Email address">
                    <p id="email-error" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input id="phone" 
                           name="phone" 
                           type="tel" 
                           value="{{ old('phone') }}"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                           placeholder="Phone number (optional)">
                    <p id="phone-error" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                           placeholder="Password">
                    <p id="password-error" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                           placeholder="Confirm Password">
                </div>
                
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input id="date_of_birth" 
                           name="date_of_birth" 
                           type="date" 
                           value="{{ old('date_of_birth') }}"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    <p id="date_of_birth-error" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                
                <div class="flex items-center">
                    <input id="marketing_emails" 
                           name="marketing_emails" 
                           type="checkbox" 
                           value="1"
                           {{ old('marketing_emails') ? 'checked' : '' }}
                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="marketing_emails" class="ml-2 block text-sm text-gray-900">
                        I would like to receive marketing emails about new products and offers
                    </label>
                </div>
            </div>

            <div>
                <button type="submit"
                        id="register-submit-btn" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    <span id="register-btn-text">Create Account</span>
                    <svg id="register-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
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
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = document.getElementById('register-submit-btn');
    const btnText = document.getElementById('register-btn-text');
    const spinner = document.getElementById('register-spinner');
    const errorAlert = document.getElementById('register-error-alert');
    const errorMessage = document.getElementById('register-error-message');
    
    // Clear previous errors
    errorAlert.classList.add('hidden');
    document.querySelectorAll('[id$="-error"]').forEach(el => {
        if (el.id !== 'register-error-alert') {
            el.classList.add('hidden');
            el.textContent = '';
        }
    });
    document.querySelectorAll('input').forEach(el => {
        el.classList.remove('border-red-500');
    });
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.textContent = 'Creating account...';
    spinner.classList.remove('hidden');
    
    const formData = new FormData(form);
    
    fetch('{{ route("customer.register") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json().then(data => ({ status: response.status, data })))
    .then(result => {
        if (result.data.success && result.data.redirect) {
            window.location.href = result.data.redirect;
        } else if (result.status === 422 && result.data.errors) {
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
            errorMessage.textContent = 'Please fix the errors below.';
        } else if (result.data.errors) {
            errorAlert.classList.remove('hidden');
            errorMessage.textContent = result.data.errors.general?.[0] || 'An error occurred.';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorAlert.classList.remove('hidden');
        errorMessage.textContent = 'An error occurred. Please try again.';
    })
    .finally(() => {
        submitBtn.disabled = false;
        btnText.textContent = 'Create Account';
        spinner.classList.add('hidden');
    });
});
</script>
@endpush
@endsection