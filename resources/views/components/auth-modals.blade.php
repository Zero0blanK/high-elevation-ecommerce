<div x-data="{
    loginOpen: false,
    registerOpen: false,
    
    openLogin() {
        this.loginOpen = true;
        this.registerOpen = false;
    },
    
    openRegister() {
        this.registerOpen = true;
        this.loginOpen = false;
    },
    
    closeAll() {
        this.loginOpen = false;
        this.registerOpen = false;
    },
    
    switchToRegister() {
        this.loginOpen = false;
        this.registerOpen = true;
    },
    
    switchToLogin() {
        this.registerOpen = false;
        this.loginOpen = true;
    }
}" 
@keydown.escape="closeAll()"
x-init="
    // Listen for global events to open modals
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
                <form action="{{ route('customer.login') }}" method="POST">
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
                        
                        <!-- Display login errors -->
                        @if($errors->has('login_error'))
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                                {{ $errors->first('login_error') }}
                            </div>
                        @endif
                        
                        <div class="space-y-4">
                            <div>
                                <label for="login_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="login_email" 
                                       name="email" 
                                       type="email" 
                                       required 
                                       value="{{ old('email') }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('email') border-red-500 @enderror" 
                                       placeholder="Enter your email">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="login_password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="login_password" 
                                       name="password" 
                                       type="password" 
                                       required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('password') border-red-500 @enderror" 
                                       placeholder="Enter your password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                    <a href="#" class="font-medium text-amber-600 hover:text-amber-500">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
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
                <form action="{{ route('customer.register') }}" method="POST">
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
                        
                        <!-- Display register errors -->
                        @if($errors->any())
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="register_first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input id="register_first_name" 
                                           name="first_name" 
                                           type="text" 
                                           required 
                                           value="{{ old('first_name') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('first_name') border-red-500 @enderror" 
                                           placeholder="First Name">
                                </div>
                                
                                <div>
                                    <label for="register_last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input id="register_last_name" 
                                           name="last_name" 
                                           type="text" 
                                           required 
                                           value="{{ old('last_name') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('last_name') border-red-500 @enderror" 
                                           placeholder="Last Name">
                                </div>
                            </div>
                            
                            <div>
                                <label for="register_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input id="register_email" 
                                       name="email" 
                                       type="email" 
                                       required 
                                       value="{{ old('email') }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('email') border-red-500 @enderror" 
                                       placeholder="Enter your email">
                            </div>
                            
                            <div>
                                <label for="register_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input id="register_phone" 
                                       name="phone" 
                                       type="tel" 
                                       value="{{ old('phone') }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="Phone number (optional)">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="register_password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input id="register_password" 
                                           name="password" 
                                           type="password" 
                                           required 
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('password') border-red-500 @enderror" 
                                           placeholder="Password">
                                </div>
                                
                                <div>
                                    <label for="register_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input id="register_password_confirmation" 
                                           name="password_confirmation" 
                                           type="password" 
                                           required 
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm @error('password_confirmation') border-red-500 @enderror" 
                                           placeholder="Confirm Password">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
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
</script>