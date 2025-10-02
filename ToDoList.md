## customers

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| first_name          |             |
| last_name           |             |
| email               |             |
| email_verified_at   |             |
| password            |             |
| phone               |             |
| date_of_birth       |             |
| is_active           |             |
| last_login_at       |             |
| remember_token      |             |
| created_at          |             |
| updated_at          |             |
| deleted_at          |             |

## customer_addresses

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| customer_id         |             |
| type                |             |
| first_name          |             |
| last_name           |             |
| company             |             |
| address_line_1      |             |
| address_line_2      |             |
| city                |             |
| state               |             |
| postal_code         |             |
| country             |             |
| is_default          |             |
| created_at          |             |
| updated_at          |             |

## orders

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| order_number        |             |
| customer_id         |             |
| customer_address_id |             |
| status              |             |
| currency            |             |
| subtotal            |             |
| tax_amount          |             |
| shipping_amount     |             |
| discount_amount     |             |
| total_amount        |             |
| payment_status      |             |
| payment_method      |             |
| shipping_method     |             |
| tracking_number     |             |
| notes               |             |
| shipped_at          |             |
| delivered_at        |             |
| created_at          |             |
| updated_at          |             |
| deleted_at          |             |

## order_items

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| order_id            |             |
| product_id          |             |
| product_name        |             |
| quantity            |             |
| unit_price          |             |
| total_price         |             |
| created_at          |             |
| updated_at          |             |

## shopping_cart

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| session_id          |             |
| customer_id         |             |
| product_id          |             |
| quantity            |             |
| product_options     |             |
| created_at          |             |
| updated_at          |             |

## inventory_logs

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| product_id          |             |
| type                |             |
| quantity_before     |             |
| quantity_changed    |             |
| quantity_after      |             |
| reference_id        |             |
| reference_type      |             |
| notes               |             |
| created_at          |             |
| updated_at          |             |

## categories

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| name                |             |
| slug                |             |
| description         |             |
| image_url           |             |
| is_active           |             |
| created_at          |             |
| updated_at          |             |

## product_images

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| product_id          |             |
| image_url           |             |
| alt_text            |             |
| sort_order          |             |
| is_primary          |             |
| created_at          |             |
| updated_at          |             |
| deleted_at          |             |

## products

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| name                |             |
| slug                |             |
| description         |             |
| short_description   |             |
| price               |             |
| sale_price          |             |
| category_id         |             |
| stock_quantity      |             |
| low_stock_threshold |             |
| weight              |             |
| roast_level         |             |
| grind_type          |             |
| origin              |             |
| is_featured         |             |
| is_active           |             |
| created_at          |             |
| updated_at          |             |
| deleted_at          |             |

## payments

| Field               | Description |
|---------------------|-------------|
| id                  |             |
| order_id            |             |
| payment_method      |             |
| payment_gateway     |             |
| transaction_id      |             |
| gateway_transaction_id |         |
| amount              |             |
| currency            |             |
| status              |             |
| gateway_response    |             |
| processed_at        |             |
| created_at          |             |
| updated_at          |             |

1. Customers → Customer Addresses (One-to-Many)

Explanation: A customer can have multiple addresses, but each address belongs to only one customer.

2. Customers → Orders (One-to-Many)

Explanation: A customer can place many orders, but each order is linked to one customer.

3. Orders → Order Items (One-to-Many)

Explanation: An order can have multiple order items (like a cart), but each order item belongs to one order.

4. Orders → Customer Addresses (One-to-Many)

Explanation: An order is shipped to one address, but multiple orders can share the same address.

5. Products → Order Items (One-to-Many)

Explanation: A product can appear in many order items, but each order item references one product.

6. Products → Product Images (One-to-Many)

Explanation: A product can have multiple product images, but each image belongs to one product.

7. Categories → Products (One-to-Many)

Explanation: A category can have many products, but each product belongs to one category.

8. Shopping Cart → Customers (One-to-Many)

Explanation: A customer can have multiple shopping cart items (products in the cart), but each cart item is linked to one customer.

9. Shopping Cart → Products (One-to-Many)

Explanation: A product can be in many shopping carts, but each cart item is linked to one product.

10. Payments → Orders (One-to-One or One-to-Many)

Explanation: Typically, one order has one payment, but depending on the payment method, an order could have multiple payments. (One-to-One or One-to-Many based on system setup).

11. Inventory Logs → Products (One-to-Many)

Explanation: A product can have many inventory log entries (to track stock changes), but each inventory log is tied to one product.