<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif;color:#222;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f5f5;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="640" style="max-width:640px;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#b91c1c;">Low Stock Alert</h2>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">
                                A product has reached or dropped below its low stock threshold.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 20px;">
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#6b7280;width:180px;">Product</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;">{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#6b7280;">SKU</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;">{{ $product->sku ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#6b7280;">Current stock</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;color:#b91c1c;">{{ $product->stock_quantity }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#6b7280;">Low stock threshold</td>
                                    <td style="padding:8px 0;font-size:14px;font-weight:600;">{{ $product->low_stock_threshold }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">
                                Please review and restock this item as soon as possible.
                            </p>

                            <p style="margin:0 0 8px;">
                                <a href="{{ $adminUrl }}" style="display:inline-block;background:#111827;color:#ffffff;text-decoration:none;padding:10px 14px;border-radius:6px;font-size:14px;">
                                    View Product
                                </a>
                            </p>
                            <p style="margin:0;">
                                <a href="{{ $inventoryUrl }}" style="font-size:14px;color:#1d4ed8;text-decoration:none;">
                                    Open Inventory Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
