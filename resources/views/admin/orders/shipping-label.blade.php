<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label — Order #{{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f3f4f6; padding: 20px; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button { background: #d97706; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; margin: 0 6px; }
        .no-print button:hover { background: #b45309; }
        .no-print .back-btn { background: #6b7280; }
        .no-print .back-btn:hover { background: #4b5563; }
        .label { background: #fff; max-width: 4in; margin: 0 auto; border: 2px solid #000; padding: 0; }
        .label-header { background: #000; color: #fff; padding: 10px 16px; text-align: center; }
        .label-header h1 { font-size: 16px; letter-spacing: 2px; text-transform: uppercase; }
        .label-body { padding: 16px; }
        .section { margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px dashed #ccc; }
        .section:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .section-title { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #666; font-weight: 700; margin-bottom: 6px; }
        .from-address, .to-address { font-size: 12px; line-height: 1.5; }
        .to-address { font-size: 14px; font-weight: 700; }
        .order-info { display: flex; justify-content: space-between; font-size: 11px; }
        .order-info .label-field { }
        .order-info .label-value { font-weight: 700; }
        .barcode { text-align: center; padding: 10px 0; }
        .barcode-text { font-family: monospace; font-size: 18px; letter-spacing: 4px; font-weight: 700; }
        .barcode-lines { margin: 6px auto; height: 40px; width: 200px; background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px, #000 4px, #000 5px, #fff 5px, #fff 8px); }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .label { border: 2px solid #000; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="back-btn" onclick="window.history.back()">← Back to Order</button>
        <button onclick="window.print()">🖨 Print Label</button>
    </div>

    <div class="label">
        <div class="label-header">
            <h1>High Elevation Coffee</h1>
        </div>
        <div class="label-body">
            {{-- From --}}
            <div class="section">
                <div class="section-title">From</div>
                <div class="from-address">
                    <strong>High Elevation Coffee Co.</strong><br>
                    {{ config('ecommerce.company.address', '123 Coffee St.') }}<br>
                    {{ config('ecommerce.company.city', 'Baguio City') }}, {{ config('ecommerce.company.state', 'Benguet') }} {{ config('ecommerce.company.zip', '2600') }}<br>
                    {{ config('ecommerce.company.country', 'Philippines') }}
                </div>
            </div>

            {{-- To --}}
            <div class="section">
                <div class="section-title">Ship To</div>
                <div class="to-address">
                    @if($shippingAddr)
                        {{ $shippingAddr->first_name ?? '' }} {{ $shippingAddr->last_name ?? '' }}<br>
                        @if($shippingAddr->address)
                            {{ $shippingAddr->address->address_line1 ?? '' }}<br>
                            @if($shippingAddr->address->address_line2){{ $shippingAddr->address->address_line2 }}<br>@endif
                            {{ $shippingAddr->address->city ?? '' }}, {{ $shippingAddr->address->state ?? '' }} {{ $shippingAddr->address->postal_code ?? '' }}<br>
                            {{ $shippingAddr->address->country ?? '' }}
                        @else
                            {{ $shippingAddr->address_line_1 ?? $shippingAddr->address_line1 ?? '' }}<br>
                            @if($shippingAddr->address_line_2 ?? $shippingAddr->address_line2 ?? null){{ $shippingAddr->address_line_2 ?? $shippingAddr->address_line2 }}<br>@endif
                            {{ $shippingAddr->city ?? '' }}, {{ $shippingAddr->state ?? '' }} {{ $shippingAddr->postal_code ?? $shippingAddr->zip ?? '' }}<br>
                            {{ $shippingAddr->country ?? '' }}
                        @endif
                        @if($shippingAddr->phone)<br>Tel: {{ $shippingAddr->phone }}@endif
                    @elseif($order->customer)
                        {{ $order->customer->first_name }} {{ $order->customer->last_name }}<br>
                        {{ $order->customer->email }}<br>
                        @if($order->customer->phone)Tel: {{ $order->customer->phone }}@endif
                    @else
                        <em>No shipping address available</em>
                    @endif
                </div>
            </div>

            {{-- Order Info --}}
            <div class="section">
                <div class="order-info">
                    <div>
                        <span class="label-field">Order:</span>
                        <span class="label-value">{{ $order->order_number }}</span>
                    </div>
                    <div>
                        <span class="label-field">Date:</span>
                        <span class="label-value">{{ $order->created_at->format('m/d/Y') }}</span>
                    </div>
                </div>
                <div class="order-info" style="margin-top: 4px;">
                    <div>
                        <span class="label-field">Items:</span>
                        <span class="label-value">{{ $order->items->sum('quantity') }} pcs</span>
                    </div>
                    <div>
                        <span class="label-field">Method:</span>
                        <span class="label-value">{{ ucfirst($order->shipping_method ?? 'Standard') }}</span>
                    </div>
                </div>
                @if($order->tracking_number)
                <div class="order-info" style="margin-top: 4px;">
                    <div>
                        <span class="label-field">Tracking:</span>
                        <span class="label-value">{{ $order->tracking_number }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Barcode --}}
            <div class="barcode">
                <div class="barcode-lines"></div>
                <div class="barcode-text">{{ $order->order_number }}</div>
            </div>
        </div>
    </div>
</body>
</html>
