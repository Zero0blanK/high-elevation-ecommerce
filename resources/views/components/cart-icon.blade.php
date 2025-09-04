@props(['count' => 0])

<div class="relative">
    <a href="{{ route('cart.index') }}" 
       class="flex items-center text-gray-700 hover:text-amber-600 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H5a2 2 0 00-2 2v1m2 0h16M7 13v8a2 2 0 002 2h8a2 2 0 002-2v-8m-9 4h4"/>
        </svg>
        
        @if($count > 0)
            <span class="absolute -top-2 -right-2 bg-amber-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </a>
</div>