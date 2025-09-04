<?php

return [
    'enabled' => env('SOFT_DELETES_ENABLED', true),
    
    'models' => [
        'product' => env('SOFT_DELETES_PRODUCTS', true),
        'category' => env('SOFT_DELETES_CATEGORIES', true),
        'user' => env('SOFT_DELETES_USERS', true),
        'order' => env('SOFT_DELETES_ORDERS', true),
        'customer' => env('SOFT_DELETES_CUSTOMERS', true),
        'productimage' => env('SOFT_DELETES_PRODUCT_IMAGES', true),
    ],
];
