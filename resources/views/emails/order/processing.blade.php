<!DOCTYPE html>
<html>
<head>
    <title>Order Processing</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w-2xl; margin: 0 auto; padding: 20px;">
        <h2>Order Processing - {{ $order->order_number }}</h2>
        <p>Hi {{ $customer->first_name ?? $customer->name }},</p>
        <p>Good news! Your order is now being processed and is being prepared for shipment.</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p><strong>Order Details:</strong></p>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                <li><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</li>
                <li><strong>Shipping Method:</strong> {{ $order->shipping_method ?? 'Standard' }}</li>
            </ul>
        </div>

        @if($trackingUrl)
        <p>You can check the latest status of your order anytime by visiting your tracking page:</p>
        <p><a href="{{ $trackingUrl }}" style="background-color: #d97706; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Track Order</a></p>
        @endif
        
        <p>We will notify you again once your order has been shipped.</p>
        
        <p>Thank you for shopping with us!</p>
    </div>
</body>
</html>
