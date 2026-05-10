<div x-data="authModals()"
@keydown.escape="closeAll()"
x-init="
    window.addEventListener('open-login-modal', () => openLogin());
    window.addEventListener('open-register-modal', () => openRegister());
">
    <!-- Login Modal -->
    <div x-show="loginOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center content-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-400/40 bg-opacity-10 transition-opacity" @click="closeAll()"></div>

            <div class="relative inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="handleLoginSubmit" action="{{ route('customer.login') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Sign In</h3>
                            <button type="button" @click="closeAll()" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="login_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="login_email" 
                                       name="email" 
                                       type="email" 
                                       required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="Enter your email"
                                       @input="$el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
                            </div>
                            
                            <div>
                                <label for="login_password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="login_password" 
                                       name="password" 
                                       type="password" 
                                       required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="Enter your password"
                                       @input="$el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
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
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Sign In
                        </button>
                        <button type="button" 
                                @click="closeAll()" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
                
                <div class="px-4 pb-4 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <button @click="switchToRegister()" class="font-medium text-amber-600 hover:text-amber-500">
                            Sign up
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div x-show="registerOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex content-center items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-400/40 bg-opacity-50 transition-opacity" @click="closeAll()"></div>

            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full max-h-screen overflow-y-auto">
                <form @submit.prevent="handleRegisterSubmit" action="{{ route('customer.register') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Create Account</h3>
                            <button type="button" @click="closeAll()" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div x-show="Object.keys(formErrors).length > 0" 
                            class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                            <ul class="list-disc list-inside text-sm">
                                <template x-for="(errors, field) in formErrors" :key="field">
                                    <li x-text="errors[0]"></li>
                                </template>
                            </ul>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="register_first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input id="register_first_name" 
                                           name="first_name" 
                                           type="text" 
                                           required 
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                           placeholder="First Name"
                                           @input="$el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
                                </div>
                                
                                <div>
                                    <label for="register_last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input id="register_last_name" 
                                           name="last_name" 
                                           type="text" 
                                           required 
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                           placeholder="Last Name"
                                           @input="$el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
                                </div>
                            </div>
                            
                            <div>
                                <label for="register_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input id="register_email" 
                                       name="email" 
                                       type="email" 
                                       required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="you@example.com"
                                       @input="$el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
                            </div>
                            
                            <div>
                                <label for="register_phone" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input id="register_phone" 
                                       name="phone" 
                                       type="tel" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="+639123456789 or 09123456789">
                                <p class="mt-1 text-xs text-gray-500">International format supported (e.g., +1234567890)</p>
                            </div>
                            
                            <div>
                                <label for="register_password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="register_password" 
                                       name="password" 
                                       type="password" 
                                       required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="••••••••"
                                       x-model="regPassword"
                                       @input="checkPassword($event.target.value); $el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
                                <!-- Password Requirements Checklist -->
                                <div class="mt-2 space-y-1" x-show="regPassword.length > 0" x-cloak>
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
                                <p x-show="regPassword.length === 0" class="mt-1 text-xs text-gray-500">Min 8 chars: 1 uppercase, 1 lowercase, 1 number, 1 symbol</p>
                            </div>
                            
                            <div>
                                <label for="register_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input id="register_password_confirmation" 
                                       name="password_confirmation" 
                                       type="password" 
                                       required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="••••••••"
                                       @input="$el.classList.remove('border-red-500'); $el.parentNode.querySelectorAll('.error-message').forEach(e => e.remove())">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Create Account
                        </button>
                        <button type="button" 
                                @click="closeAll()" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
                
                <div class="px-4 pb-4 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <button @click="switchToLogin()" class="font-medium text-amber-600 hover:text-amber-500">
                            Sign in
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global functions to trigger modal opening
window.openLoginModal = function() {
    window.dispatchEvent(new CustomEvent('open-login-modal'));
}

window.openRegisterModal = function() {
    window.dispatchEvent(new CustomEvent('open-register-modal'));
}

function authModals() {
    return {
        loginOpen: false,
        registerOpen: false,
        formErrors: {},
        loginErrors: {},
        isSubmitting: false,
        isLoginSubmitting: false,
        regPassword: '',
        pwdChecks: { length: false, upper: false, lower: false, number: false, symbol: false },
        
        checkPassword(pwd) {
            this.regPassword = pwd;
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
        
        openLogin() {
            this.loginOpen = true;
            this.registerOpen = false;
            this.loginErrors = {};
        },
        
        openRegister() {
            this.registerOpen = true;
            this.loginOpen = false;
            this.formErrors = {};
            this.regPassword = '';
            this.pwdChecks = { length: false, upper: false, lower: false, number: false, symbol: false };
        },
        
        closeAll() {
            this.loginOpen = false;
            this.registerOpen = false;
            this.formErrors = {};
            this.loginErrors = {};
            this.regPassword = '';
            this.pwdChecks = { length: false, upper: false, lower: false, number: false, symbol: false };
        },
        
        switchToRegister() {
            this.loginOpen = false;
            this.registerOpen = true;
            this.formErrors = {};
            this.regPassword = '';
            this.pwdChecks = { length: false, upper: false, lower: false, number: false, symbol: false };
        },
        
        switchToLogin() {
            this.registerOpen = false;
            this.loginOpen = true;
            this.loginErrors = {};
        },

        handleLoginSubmit() {
            this.isLoginSubmitting = true;
            this.loginErrors = {};
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Signing in...';
            submitBtn.disabled = true;

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid', 'border-red-500'));
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.general-error').forEach(el => el.remove());

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(result => {
                if (result.data.success && result.data.redirect) {
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md general-error';
                    successDiv.textContent = 'Login successful! Redirecting...';
                    form.querySelector('.space-y-4').insertBefore(successDiv, form.querySelector('.space-y-4').firstChild);
                    setTimeout(() => { window.location.href = result.data.redirect; }, 1000);
                } else if (result.status === 422 && result.data.errors) {
                    this.loginErrors = result.data.errors;
                    this.showFieldErrors(form, result.data.errors);
                    this.resetButton(submitBtn, originalBtnText);
                } else {
                    this.showGeneralError(form, result.data.message || 'An error occurred. Please try again.');
                    this.resetButton(submitBtn, originalBtnText);
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                this.showGeneralError(form, 'An error occurred. Please try again.');
                this.resetButton(submitBtn, originalBtnText);
            });
        },

        handleRegisterSubmit() {
            this.isSubmitting = true;
            this.formErrors = {};
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating account...';
            submitBtn.disabled = true;

            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid', 'border-red-500'));
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.general-error').forEach(el => el.remove());

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(result => {
                if (result.data.success && result.data.redirect) {
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md general-error';
                    successDiv.textContent = 'Account created successfully! Redirecting...';
                    form.querySelector('.space-y-4').insertBefore(successDiv, form.querySelector('.space-y-4').firstChild);
                    setTimeout(() => { window.location.href = result.data.redirect; }, 1500);
                } else if (result.status === 422 && result.data.errors) {
                    this.formErrors = result.data.errors;
                    this.showFieldErrors(form, result.data.errors);
                    this.resetButton(submitBtn, originalBtnText);
                } else {
                    this.resetButton(submitBtn, originalBtnText);
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                this.showGeneralError(form, 'An error occurred. Please try again.');
                this.resetButton(submitBtn, originalBtnText);
            });
        },
        
        showFieldErrors(form, errors) {
            Object.keys(errors).forEach(field => {
                const input = form.querySelector('[name="' + field + '"]');
                if (input) {
                    input.classList.add('is-invalid', 'border-red-500');
                    const errorP = document.createElement('p');
                    errorP.className = 'mt-1 text-sm text-red-600 error-message';
                    errorP.textContent = errors[field][0];
                    input.parentNode.insertBefore(errorP, input.nextSibling);
                }
            });
        },
        
        showGeneralError(form, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md general-error';
            errorDiv.textContent = message;
            const spaceY4 = form.querySelector('.space-y-4');
            if (spaceY4) spaceY4.insertBefore(errorDiv, spaceY4.firstChild);
        },
        
        resetButton(btn, originalText) {
            this.isSubmitting = false;
            this.isLoginSubmitting = false;
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    };
}
</script>