@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16" x-data="{
    formErrors: {},
    isSubmitting: false,

    handleProfileSubmit() {
        this.isSubmitting = true;
        this.formErrors = {};
        const form = event.target;
        const submitBtn = form.querySelector('button[type=\'submit\']');
        const originalBtnText = submitBtn.innerHTML;

        submitBtn.innerHTML = `
            <svg class='animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
            </svg>
            Updating...
        `;
        submitBtn.disabled = true;

        // Reset previous error states
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid', 'border-red-500');
        });
        form.querySelectorAll('.error-message').forEach(el => el.remove());

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form)))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md';
                successDiv.textContent = 'Profile updated successfully!';
                form.insertBefore(successDiv, form.firstChild);
                
                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            } else {
                // Show validation errors
                this.formErrors = data.errors;
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name='${field}']`);
                    if (input) {
                        input.classList.add('is-invalid', 'border-red-500');
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'mt-1 text-sm text-red-600 error-message';
                        errorDiv.textContent = data.errors[field][0];
                        input.parentNode.insertBefore(errorDiv, input.nextSibling);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md';
            errorDiv.textContent = 'An error occurred. Please try again.';
            form.insertBefore(errorDiv, form.firstChild);
        })
        .finally(() => {
            this.isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-600 mt-2">Manage your personal information</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form @submit.prevent="handleProfileSubmit" action="{{ route('account.profile.update') }}" class="space-y-6 p-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" 
                           name="first_name" 
                           id="first_name" 
                           required
                           minlength="2"
                           pattern="[A-Za-z\s-]+"
                           title="Please enter a valid first name (letters, spaces, and hyphens only)"
                           value="{{ old('first_name', $customer->first_name) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                           :class="{ 'border-red-500': formErrors.first_name }">
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" 
                           name="last_name" 
                           id="last_name" 
                           required
                           minlength="2"
                           pattern="[A-Za-z\s-]+"
                           title="Please enter a valid last name (letters, spaces, and hyphens only)"
                           value="{{ old('last_name', $customer->last_name) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                           :class="{ 'border-red-500': formErrors.last_name }">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       required
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                       value="{{ old('email', $customer->email) }}"
                       class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                       :class="{ 'border-red-500': formErrors.email }">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           pattern="[0-9]*"
                           inputmode="numeric"
                           title="Please enter numbers only"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           value="{{ old('phone', $customer->phone) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                           :class="{ 'border-red-500': formErrors.phone }">

                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" 
                           name="date_of_birth" 
                           id="date_of_birth" 
                           value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-md font-medium">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white shadow rounded-lg mt-8" x-data="{
        passwordFormErrors: {},
        isPasswordSubmitting: false,
        
        handlePasswordSubmit() {
            this.isPasswordSubmitting = true;
            this.passwordFormErrors = {};
            const form = event.target;
            const submitBtn = form.querySelector('button[type=\'submit\']');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = `
                <svg class='animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                    <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                    <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
                </svg>
                Updating password...
            `;
            submitBtn.disabled = true;

            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid', 'border-red-500');
            });
            form.querySelectorAll('.error-message').forEach(el => el.remove());

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md';
                    successDiv.textContent = data.message || 'Password updated successfully!';
                    form.insertBefore(successDiv, form.firstChild);
                    form.reset();
                    
                    setTimeout(() => {
                        successDiv.remove();
                    }, 3000);
                } else {
                    // Handle validation errors
                    this.passwordFormErrors = data.errors || {};
                    Object.keys(this.passwordFormErrors).forEach(field => {
                        const input = form.querySelector(`[name='${field}']`);
                        if (input) {
                            input.classList.add('is-invalid', 'border-red-500');
                            const errorDiv = document.createElement('p');
                            errorDiv.className = 'mt-1 text-sm text-red-600 error-message';
                            errorDiv.textContent = this.passwordFormErrors[field][0];
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                        }
                    });

                    // Show general error if no specific field errors
                    if (Object.keys(this.passwordFormErrors).length === 0) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md';
                        errorDiv.textContent = data.message || 'Failed to update password. Please try again.';
                        form.insertBefore(errorDiv, form.firstChild);
                    }
                }
            })
            .catch(error => {
                console.error('Password update error:', error);
                
                // Handle validation errors from catch block
                if (error && error.errors) {
                    this.passwordFormErrors = error.errors;
                    Object.keys(this.passwordFormErrors).forEach(field => {
                        const input = form.querySelector(`[name='${field}']`);
                        if (input) {
                            input.classList.add('is-invalid', 'border-red-500');
                            const errorDiv = document.createElement('p');
                            errorDiv.className = 'mt-1 text-sm text-red-600 error-message';
                            errorDiv.textContent = this.passwordFormErrors[field][0];
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                        }
                    });
                } else {
                    // Show general error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md';
                    errorDiv.textContent = error.message || 'An unexpected error occurred. Please try again.';
                    form.insertBefore(errorDiv, form.firstChild);
                }
            })
            .finally(() => {
                this.isPasswordSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        }
    }">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
        </div>
        
        <form @submit.prevent="handlePasswordSubmit" action="{{ route('account.password.update') }}" class="space-y-6 p-6">
            @csrf
            @method('PATCH')

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" 
                       name="current_password" 
                       id="current_password"
                       required
                       minlength="8"
                       class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                       :class="{ 'border-red-500': passwordFormErrors.current_password }">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           required
                           minlength="8"
                           title="Password must contain at least 8 characters"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                           :class="{ 'border-red-500': passwordFormErrors.password }">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           required
                           minlength="8"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                           :class="{ 'border-red-500': passwordFormErrors.password_confirmation }">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-md font-medium">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection