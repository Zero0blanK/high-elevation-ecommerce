<?php

return [
    'store' => [
        'name' => env('STORE_NAME', 'Coffee Beans Store'),
        'email' => env('STORE_EMAIL', 'info@coffeestore.com'),
        'phone' => env('STORE_PHONE', '+1 (555) 123-4567'),
        'address' => [
            'line_1' => env('STORE_ADDRESS_LINE_1', '123 Coffee Street'),
            'line_2' => env('STORE_ADDRESS_LINE_2', ''),
            'city' => env('STORE_CITY', 'Coffee City'),
            'state' => env('STORE_STATE', 'CA'),
            'postal_code' => env('STORE_POSTAL_CODE', '12345'),
            'country' => env('STORE_COUNTRY', 'United States'),
        ],
    ],

    'currency' => [
        'default' => env('STORE_CURRENCY', 'USD'),
        'symbol' => env('STORE_CURRENCY_SYMBOL', '$'),
        'position' => env('STORE_CURRENCY_POSITION', 'before'), // before or after
    ],

    'tax' => [
        'enabled' => env('TAX_ENABLED', true),
        'rate' => env('TAX_RATE', 0.08), // 8% default
        'display_prices_with_tax' => env('DISPLAY_PRICES_WITH_TAX', false),
    ],

    'shipping' => [
        'free_shipping_threshold' => env('FREE_SHIPPING_THRESHOLD', 50.00),
        'default_weight_unit' => env('DEFAULT_WEIGHT_UNIT', 'lb'),
        'rates' => [
            'standard' => [
                'name' => 'Standard Shipping',
                'base_rate' => 5.99,
                'rate_per_lb' => 2.00,
                'delivery_days' => '5-7',
            ],
            'express' => [
                'name' => 'Express Shipping',
                'base_rate' => 12.99,
                'rate_per_lb' => 3.00,
                'delivery_days' => '2-3',
            ],
            'overnight' => [
                'name' => 'Overnight Shipping',
                'base_rate' => 24.99,
                'rate_per_lb' => 5.00,
                'delivery_days' => '1',
            ],
        ],
    ],

    'inventory' => [
        'track_quantity' => env('TRACK_INVENTORY', true),
        'allow_backorders' => env('ALLOW_BACKORDERS', false),
        'low_stock_threshold' => env('DEFAULT_LOW_STOCK_THRESHOLD', 10),
        'auto_reduce_stock' => env('AUTO_REDUCE_STOCK', true),
    ],

    'orders' => [
        'auto_complete_virtual' => env('AUTO_COMPLETE_VIRTUAL_ORDERS', true),
        'delete_incomplete_orders' => env('DELETE_INCOMPLETE_ORDERS_AFTER_DAYS', 7),
        'guest_checkout' => env('GUEST_CHECKOUT_ENABLED', true),
    ],

    'emails' => [
        'new_order' => [
            'enabled' => env('EMAIL_NEW_ORDER_ENABLED', true),
            'recipients' => explode(',', env('EMAIL_NEW_ORDER_RECIPIENTS', 'admin@coffeestore.com')),
        ],
        'low_stock' => [
            'enabled' => env('EMAIL_LOW_STOCK_ENABLED', true),
            'recipients' => explode(',', env('EMAIL_LOW_STOCK_RECIPIENTS', 'admin@coffeestore.com')),
        ],
    ],

    'seo' => [
        'meta_title' => env('SEO_META_TITLE', 'Premium Coffee Beans Store'),
        'meta_description' => env('SEO_META_DESCRIPTION', 'Discover the finest coffee beans from around the world. Premium quality, freshly roasted, and delivered to your door.'),
        'meta_keywords' => env('SEO_META_KEYWORDS', 'coffee, coffee beans, arabica, robusta, single origin, coffee roasting'),
    ],

    'social' => [
        'facebook' => env('SOCIAL_FACEBOOK', ''),
        'twitter' => env('SOCIAL_TWITTER', ''),
        'instagram' => env('SOCIAL_INSTAGRAM', ''),
        'youtube' => env('SOCIAL_YOUTUBE', ''),
    ],

    'analytics' => [
        'google_analytics' => env('GOOGLE_ANALYTICS_TRACKING_ID', ''),
        'facebook_pixel' => env('FACEBOOK_PIXEL_ID', ''),
    ],

    'cache' => [
        'products_ttl' => env('CACHE_PRODUCTS_TTL', 3600), // 1 hour
        'categories_ttl' => env('CACHE_CATEGORIES_TTL', 7200), // 2 hours
        'cart_ttl' => env('CACHE_CART_TTL', 1800), // 30 minutes
    ],
];