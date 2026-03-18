@extends('layouts.app')

@section('title', 'About Us - High Elevation Coffee')
@section('description', 'Learn about High Elevation Coffee - premium coffee beans sourced from the world\'s finest high-altitude regions.')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-amber-900 via-amber-800 to-yellow-900 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r="1" fill="white"/></pattern></defs><rect fill="url(#dots)" width="100" height="100"/></svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 relative">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-700/50 text-amber-200 mb-4">Our Story</span>
            <h1 class="text-4xl sm:text-5xl font-bold text-white leading-tight">
                Crafted at <span class="text-amber-300">High Elevation</span>, Delivered with Passion
            </h1>
            <p class="mt-6 text-lg text-amber-100/90 leading-relaxed">
                We believe the best coffee comes from the highest peaks. Our beans are sourced from small-batch farms
                nestled in the mountains, where cool temperatures and rich volcanic soil produce beans with unmatched
                depth, complexity, and flavor.
            </p>
        </div>
    </div>
</div>

<!-- Mission Section -->
<div class="bg-white py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <div>
                <span class="text-sm font-semibold text-amber-600 uppercase tracking-wider">Our Mission</span>
                <h2 class="mt-2 text-3xl font-bold text-gray-900">Coffee That Makes a Difference</h2>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    At High Elevation Coffee, our mission is simple: to bring you the finest coffee while supporting
                    the farmers and communities that grow it. We work directly with highland coffee producers, paying
                    fair prices and investing in sustainable farming practices.
                </p>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Every cup you enjoy helps sustain mountain farming communities and preserve the unique terroir
                    that makes high-altitude coffee so exceptional.
                </p>
                <div class="mt-8 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-600">12+</p>
                        <p class="text-sm text-gray-500 mt-1">Partner Farms</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-600">5k+</p>
                        <p class="text-sm text-gray-500 mt-1">Happy Customers</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-600">100%</p>
                        <p class="text-sm text-gray-500 mt-1">Ethically Sourced</p>
                    </div>
                </div>
            </div>
            <div class="mt-10 lg:mt-0">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 border border-amber-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl p-5 shadow-sm">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Sustainable</h3>
                            <p class="text-xs text-gray-500 mt-1">Eco-friendly farming & packaging</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 shadow-sm">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Fair Trade</h3>
                            <p class="text-xs text-gray-500 mt-1">Direct partnerships with farmers</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 shadow-sm">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Fresh Roasted</h3>
                            <p class="text-xs text-gray-500 mt-1">Small-batch roasting weekly</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 shadow-sm">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Crafted with Love</h3>
                            <p class="text-xs text-gray-500 mt-1">Passion in every cup</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coffee Journey Section -->
<div class="bg-gray-50 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wider">From Farm to Cup</span>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">Our Coffee Journey</h2>
            <p class="mt-3 text-gray-600 max-w-2xl mx-auto">Every bean tells a story — from the misty highlands where it grows to the moment it reaches your cup.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 text-center group hover:shadow-md hover:border-amber-200 transition-all">
                <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-200 transition-colors">
                    <span class="text-2xl">🌱</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">1. Sourced</h3>
                <p class="text-sm text-gray-500">Hand-picked from high-altitude farms above 1,500m in premium coffee regions.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 text-center group hover:shadow-md hover:border-amber-200 transition-all">
                <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-200 transition-colors">
                    <span class="text-2xl">🔥</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">2. Roasted</h3>
                <p class="text-sm text-gray-500">Small-batch roasted to bring out each origin's unique flavor profile.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 text-center group hover:shadow-md hover:border-amber-200 transition-all">
                <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-200 transition-colors">
                    <span class="text-2xl">📦</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">3. Packed</h3>
                <p class="text-sm text-gray-500">Sealed fresh in eco-friendly packaging to preserve peak flavor.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 text-center group hover:shadow-md hover:border-amber-200 transition-all">
                <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-200 transition-colors">
                    <span class="text-2xl">☕</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">4. Enjoyed</h3>
                <p class="text-sm text-gray-500">Delivered to your door, ready to brew the perfect cup.</p>
            </div>
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="bg-white py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wider">What We Stand For</span>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">Our Values</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Quality First</h3>
                <p class="text-sm text-gray-600">We never compromise on quality. Every bean is carefully selected, tested, and roasted to meet our exacting standards.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Planet Conscious</h3>
                <p class="text-sm text-gray-600">From sustainable farming to recyclable packaging, we're committed to minimizing our environmental footprint.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Community Driven</h3>
                <p class="text-sm text-gray-600">We invest in the communities that grow our coffee, supporting education, healthcare, and sustainable livelihoods.</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-r from-amber-600 to-amber-700 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white">Ready to Taste the Difference?</h2>
        <p class="mt-3 text-lg text-amber-100">Explore our collection of premium high-altitude coffee beans.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-8 py-3 bg-white text-amber-700 font-semibold rounded-full hover:bg-amber-50 transition-colors shadow-lg">
                Shop Now
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-white font-semibold rounded-full hover:bg-white/10 transition-colors">
                Contact Us
            </a>
        </div>
    </div>
</div>
@endsection
