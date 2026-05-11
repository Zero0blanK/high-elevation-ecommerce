<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\PayMongoGateway;
use App\Services\Payment\PayPalGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImprovedOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function payment_gateway_factory_resolves_correct_gateways()
    {
        $paypal = PaymentGatewayFactory::create('paypal');
        $this->assertInstanceOf(PayPalGateway::class, $paypal);

        $paymongo = PaymentGatewayFactory::create('paymongo');
        $this->assertInstanceOf(PayMongoGateway::class, $paymongo);

        $gcash = PaymentGatewayFactory::create('gcash');
        $this->assertInstanceOf(PayMongoGateway::class, $gcash);
    }

    /** @test */
    public function admin_can_quick_deliver_order_with_tracking_number()
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => 'processing', 'payment_status' => 'paid']);

        $response = $this->actingAs($admin, 'admin')
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'shipped',
                'tracking_number' => 'JNT123456789',
                'shipping_method' => 'jnt',
                'quick_deliver' => true
            ]);

        $response->assertRedirect();
        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertEquals('JNT123456789', $order->fresh()->tracking_number);
    }

    /** @test */
    public function admin_can_mark_order_shipped_with_custom_courier_name()
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => 'processing', 'payment_status' => 'paid']);

        $response = $this->actingAs($admin, 'admin')
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'shipped',
                'tracking_number' => 'TEMP-TRACK-001',
                'shipping_method' => 'lazada logistics',
            ]);

        $response->assertRedirect();
        $this->assertEquals('shipped', $order->fresh()->status);
        $this->assertEquals('TEMP-TRACK-001', $order->fresh()->tracking_number);
        $this->assertEquals('lazada logistics', $order->fresh()->shipping_method);
    }

    /** @test */
    public function customer_can_see_enhanced_tracking_data()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'tracking_number' => 'LBC987654321',
            'shipping_method' => 'lbc',
            'status' => 'shipped'
        ]);

        $response = $this->actingAs($customer, 'customer')
            ->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('LBC Express');
        $response->assertSee('Accepted at LBC Branch');
        $response->assertSee('Out for Delivery');
    }

    /** @test */
    public function checkout_uses_form_request_validation()
    {
        $customer = Customer::factory()->create();

        // Missing shipping_address_id
        $response = $this->actingAs($customer, 'customer')
            ->postJson(route('checkout.process'), [
                'payment_method' => 'cod'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shipping_address_id']);
    }

    /** @test */
    public function failed_gcash_payment_restores_items_to_cart_and_marks_order_failed()
    {
        Http::fake([
            'https://api.paymongo.com/v1/sources' => Http::response([
                'data' => [
                    'id' => 'src_test_failed_001',
                    'attributes' => [
                        'redirect' => [
                            'checkout_url' => 'https://paymongo.test/checkout/src_test_failed_001',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10, 'price' => 120, 'sale_price' => null]);
        $address = CustomerAddress::create([
            'customer_id' => $customer->id,
            'type' => 'both',
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'address_line_1' => '123 Test Street',
            'city' => 'Baguio',
            'state' => 'Benguet',
            'postal_code' => '2600',
            'country' => 'PH',
            'is_default' => true,
        ]);

        ShoppingCart::create([
            'session_id' => 'test-session',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'product_options' => [],
        ]);

        $checkoutResponse = $this->actingAs($customer, 'customer')
            ->postJson(route('checkout.process'), [
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id,
                'payment_method' => 'gcash',
                'same_as_shipping' => true,
            ]);

        $checkoutResponse->assertStatus(200)->assertJson(['success' => true]);

        $orderNumber = $checkoutResponse->json('order_number');
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals(0, ShoppingCart::where('customer_id', $customer->id)->count());
        $this->assertEquals(8, $product->fresh()->stock_quantity);
        $this->assertEquals('pending', $payment->status);

        $failedResponse = $this->actingAs($customer, 'customer')
            ->get(route('checkout.paymongo.failed', ['orderNumber' => $order->order_number]));

        $failedResponse->assertRedirect(route('checkout.index'));

        $this->assertEquals('failed', $order->fresh()->payment_status);
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals('failed', $payment->fresh()->status);
        $this->assertEquals(1, ShoppingCart::where('customer_id', $customer->id)->count());
        $this->assertEquals(2, ShoppingCart::where('customer_id', $customer->id)->first()->quantity);
        $this->assertEquals(10, $product->fresh()->stock_quantity);
    }

    /** @test */
    public function paymongo_success_callback_does_not_force_paid_when_payment_is_unconfirmed()
    {
        Http::fake([
            'https://api.paymongo.com/v1/sources' => Http::response([
                'data' => [
                    'id' => 'src_test_pending_001',
                    'attributes' => [
                        'redirect' => [
                            'checkout_url' => 'https://paymongo.test/checkout/src_test_pending_001',
                        ],
                    ],
                ],
            ], 200),
            'https://api.paymongo.com/v1/sources/src_test_pending_001' => Http::response([
                'data' => [
                    'id' => 'src_test_pending_001',
                    'attributes' => [
                        'status' => 'pending',
                    ],
                ],
            ], 200),
        ]);

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10, 'price' => 100, 'sale_price' => null]);
        $address = CustomerAddress::create([
            'customer_id' => $customer->id,
            'type' => 'both',
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'address_line_1' => '456 Pending Street',
            'city' => 'Baguio',
            'state' => 'Benguet',
            'postal_code' => '2600',
            'country' => 'PH',
            'is_default' => true,
        ]);

        ShoppingCart::create([
            'session_id' => 'test-session-2',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'product_options' => [],
        ]);

        $checkoutResponse = $this->actingAs($customer, 'customer')
            ->postJson(route('checkout.process'), [
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id,
                'payment_method' => 'gcash',
                'same_as_shipping' => true,
            ]);

        $checkoutResponse->assertStatus(200)->assertJson(['success' => true]);
        $order = Order::where('order_number', $checkoutResponse->json('order_number'))->firstOrFail();

        $successResponse = $this->actingAs($customer, 'customer')
            ->get(route('checkout.paymongo.success', ['orderNumber' => $order->order_number]));

        $successResponse->assertStatus(200);
        $this->assertEquals('pending', $order->fresh()->payment_status);
        $this->assertEquals('pending', $order->fresh()->status);
    }
}
