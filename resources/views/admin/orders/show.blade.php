@extends('admin.layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
@php
    $statusColors = [
        'pending'    => 'bg-yellow-100 text-yellow-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'shipped'    => 'bg-purple-100 text-purple-800',
        'delivered'  => 'bg-green-100 text-green-800',
        'cancelled'  => 'bg-red-100 text-red-800',
        'refunded'   => 'bg-gray-100 text-gray-800',
    ];
    $paymentColors = [
        'pending'  => 'bg-yellow-100 text-yellow-800',
        'paid'     => 'bg-green-100 text-green-800',
        'completed'=> 'bg-green-100 text-green-800',
        'failed'   => 'bg-red-100 text-red-800',
        'refunded' => 'bg-gray-100 text-gray-800',
    ];
    $timelineSteps = ['pending', 'processing', 'shipped', 'delivered'];
    $currentIdx = array_search($order->status, $timelineSteps);
    $isCancelled = in_array($order->status, ['cancelled', 'refunded']);

    $shippingAddr = $order->addresses->first(fn($a) => ($a->type ?? $a->address_type ?? '') === 'shipping') ?? $order->addresses->first();
    $billingAddr  = $order->addresses->first(fn($a) => ($a->type ?? $a->address_type ?? '') === 'billing');
@endphp

<div class="space-y-6" x-data="{ showRefundModal: false, selectedStatus: '{{ $order->status }}' }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                <p class="mt-0.5 text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($order->status) }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                Payment: {{ ucfirst($order->payment_status) }}
            </span>
        </div>
    </div>

    {{-- Status Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            @foreach($timelineSteps as $idx => $step)
                @php
                    if ($isCancelled) {
                        $stepState = 'inactive';
                    } elseif ($currentIdx !== false && $idx < $currentIdx) {
                        $stepState = 'completed';
                    } elseif ($currentIdx !== false && $idx === $currentIdx) {
                        $stepState = 'current';
                    } else {
                        $stepState = 'inactive';
                    }
                @endphp
                <div class="flex flex-col items-center flex-1">
                    <div class="flex items-center w-full">
                        @if($idx > 0)
                            <div class="flex-1 h-0.5 {{ $stepState === 'inactive' ? 'bg-gray-200' : 'bg-amber-500' }}"></div>
                        @endif
                        <div class="flex items-center justify-center h-8 w-8 rounded-full shrink-0
                            {{ $stepState === 'completed' ? 'bg-amber-500 text-white' : '' }}
                            {{ $stepState === 'current' ? 'bg-amber-500 text-white ring-4 ring-amber-100' : '' }}
                            {{ $stepState === 'inactive' ? 'bg-gray-200 text-gray-400' : '' }}">
                            @if($stepState === 'completed')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            @else
                                <span class="text-xs font-bold">{{ $idx + 1 }}</span>
                            @endif
                        </div>
                        @if($idx < count($timelineSteps) - 1)
                            <div class="flex-1 h-0.5 {{ ($stepState === 'completed') ? 'bg-amber-500' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-medium {{ $stepState === 'inactive' ? 'text-gray-400' : 'text-gray-700' }}">{{ ucfirst($step) }}</span>
                </div>
            @endforeach
        </div>
        @if($isCancelled)
            <div class="mt-4 flex items-center gap-2 text-sm {{ $order->status === 'cancelled' ? 'text-red-600' : 'text-gray-600' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                This order has been <strong>{{ $order->status }}</strong>.
            </div>
        @endif
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Order Items</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50/75">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->primaryImage)
                                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_url) }}" alt="{{ $item->product_name }}" class="h-10 w-10 rounded-lg object-cover border border-gray-100">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                                    <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V5.25a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v14.25c0 .828.672 1.5 1.5 1.5z"/></svg>
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium text-gray-900">{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">{{ $item->product_sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Order Summary</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Tax</dt>
                        <dd class="font-medium text-gray-900">${{ number_format($order->tax_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Shipping</dt>
                        <dd class="font-medium text-gray-900">${{ number_format($order->shipping_amount, 2) }}</dd>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Discount</dt>
                            <dd class="font-medium text-green-600">−${{ number_format($order->discount_amount, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <dt class="text-base font-semibold text-gray-900">Total</dt>
                        <dd class="text-lg font-bold text-amber-600">${{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <dt class="text-sm text-gray-500">Payment Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </dd>
                    </div>
                    @if($order->payment_method)
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Payment Method</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ucfirst($order->payment_method) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Payment History --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Payment History</h2>
                </div>
                @if($order->payments->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50/75">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($order->payments as $payment)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ucfirst($payment->payment_method ?? '—') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">{{ $payment->transaction_id ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">${{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $paymentColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $payment->created_at ? $payment->created_at->format('M d, Y') : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-10 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        <p class="mt-2 text-sm text-gray-500">No payment records found.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Status Update --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Update Status</h2>
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                        <select name="status" id="status" x-model="selectedStatus"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="selectedStatus === 'shipped'" x-transition>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                        <input type="text" name="tracking_number" id="tracking_number" value="{{ $order->tracking_number }}"
                               placeholder="Enter tracking number"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Add a note about this status change…"
                                  class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                        Update Status
                    </button>
                </form>
            </div>

            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Customer Info</h2>
                @if($order->customer)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600 font-bold text-sm">
                            {{ strtoupper(substr($order->customer->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($order->customer->last_name ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->customer->email }}</p>
                        </div>
                    </div>
                    @if($order->customer->phone)
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            {{ $order->customer->phone }}
                        </div>
                    @endif
                    <a href="{{ route('admin.customers.show', $order->customer) }}"
                       class="inline-flex items-center gap-1 text-sm font-medium text-amber-600 hover:text-amber-800 transition-colors">
                        View profile
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @else
                    <p class="text-sm text-gray-500">Customer information unavailable.</p>
                @endif
            </div>

            {{-- Shipping Address --}}
            @if($shippingAddr)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                        <h2 class="text-base font-semibold text-gray-900">Shipping Address</h2>
                    </div>
                    <div class="text-sm text-gray-700 space-y-0.5">
                        <p class="font-medium">{{ $shippingAddr->first_name ?? '' }} {{ $shippingAddr->last_name ?? '' }}</p>
                        @if($shippingAddr->address)
                            <p>{{ $shippingAddr->address->address_line1 ?? '' }}</p>
                            @if($shippingAddr->address->address_line2)<p>{{ $shippingAddr->address->address_line2 }}</p>@endif
                            <p>{{ $shippingAddr->address->city ?? '' }}, {{ $shippingAddr->address->state ?? '' }} {{ $shippingAddr->address->postal_code ?? '' }}</p>
                            <p>{{ $shippingAddr->address->country ?? '' }}</p>
                        @else
                            <p>{{ $shippingAddr->address_line_1 ?? $shippingAddr->address_line1 ?? $shippingAddr->street ?? '' }}</p>
                            @if($shippingAddr->address_line_2 ?? $shippingAddr->address_line2 ?? null)<p>{{ $shippingAddr->address_line_2 ?? $shippingAddr->address_line2 }}</p>@endif
                            <p>{{ $shippingAddr->city ?? '' }}, {{ $shippingAddr->state ?? '' }} {{ $shippingAddr->postal_code ?? $shippingAddr->zip ?? '' }}</p>
                            <p>{{ $shippingAddr->country ?? '' }}</p>
                        @endif
                        @if($shippingAddr->phone)<p class="mt-1 text-gray-500">{{ $shippingAddr->phone }}</p>@endif
                    </div>
                </div>
            @endif

            {{-- Billing Address (only if different from shipping) --}}
            @if($billingAddr)
                @php
                    $sameAddress = $shippingAddr &&
                        ($billingAddr->address_line_1 ?? $billingAddr->address_line1 ?? '') === ($shippingAddr->address_line_1 ?? $shippingAddr->address_line1 ?? '') &&
                        ($billingAddr->city ?? '') === ($shippingAddr->city ?? '') &&
                        ($billingAddr->postal_code ?? '') === ($shippingAddr->postal_code ?? '');
                @endphp
                @unless($sameAddress)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                            <h2 class="text-base font-semibold text-gray-900">Billing Address</h2>
                        </div>
                        <div class="text-sm text-gray-700 space-y-0.5">
                            <p class="font-medium">{{ $billingAddr->first_name ?? '' }} {{ $billingAddr->last_name ?? '' }}</p>
                            @if($billingAddr->address)
                                <p>{{ $billingAddr->address->address_line1 ?? '' }}</p>
                                @if($billingAddr->address->address_line2)<p>{{ $billingAddr->address->address_line2 }}</p>@endif
                                <p>{{ $billingAddr->address->city ?? '' }}, {{ $billingAddr->address->state ?? '' }} {{ $billingAddr->address->postal_code ?? '' }}</p>
                                <p>{{ $billingAddr->address->country ?? '' }}</p>
                            @else
                                <p>{{ $billingAddr->address_line_1 ?? $billingAddr->address_line1 ?? $billingAddr->street ?? '' }}</p>
                                @if($billingAddr->address_line_2 ?? $billingAddr->address_line2 ?? null)<p>{{ $billingAddr->address_line_2 ?? $billingAddr->address_line2 }}</p>@endif
                                <p>{{ $billingAddr->city ?? '' }}, {{ $billingAddr->state ?? '' }} {{ $billingAddr->postal_code ?? $billingAddr->zip ?? '' }}</p>
                                <p>{{ $billingAddr->country ?? '' }}</p>
                            @endif
                            @if($billingAddr->phone)<p class="mt-1 text-gray-500">{{ $billingAddr->phone }}</p>@endif
                        </div>
                    </div>
                @endunless
            @endif

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.orders.shipping-label', $order) }}"
                       class="flex items-center justify-center gap-2 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2.5 rounded-lg transition-colors text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 12h.008v.008h-.008V12zm-2.25 0h.008v.008H16.5V12z"/></svg>
                        Print Shipping Label
                    </a>
                    <button @click="showRefundModal = true"
                            class="flex items-center justify-center gap-2 w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium px-4 py-2.5 rounded-lg transition-colors text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                        Issue Refund
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Refund Modal --}}
    <template x-teleport="body">
        <div x-show="showRefundModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="refund-modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                {{-- Backdrop --}}
                <div x-show="showRefundModal" @click="showRefundModal = false"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                {{-- Panel --}}
                <div x-show="showRefundModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="relative w-full max-w-md rounded-xl bg-white shadow-2xl ring-1 ring-gray-900/5 p-6">

                    <button @click="showRefundModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <form action="{{ route('admin.orders.refund', $order) }}" method="POST">
                        @csrf
                        <div class="text-center mb-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                            </div>
                            <h3 id="refund-modal-title" class="mt-3 text-lg font-semibold text-gray-900">Issue Refund</h3>
                            <p class="mt-1 text-sm text-gray-500">Order total: <strong class="text-gray-900">${{ number_format($order->total_amount, 2) }}</strong></p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="refund_amount" class="block text-sm font-medium text-gray-700 mb-1">Refund Amount</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 text-sm">$</span>
                                    </div>
                                    <input type="number" name="amount" id="refund_amount" step="0.01" min="0" max="{{ $order->total_amount }}"
                                           placeholder="{{ number_format($order->total_amount, 2) }}"
                                           class="w-full rounded-lg border-gray-300 pl-7 text-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Leave empty for full refund.</p>
                            </div>
                            <div>
                                <label for="refund_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                                <textarea name="reason" id="refund_reason" rows="3" required placeholder="Explain the reason for the refund…"
                                          class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" @click="showRefundModal = false"
                                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                                Process Refund
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
