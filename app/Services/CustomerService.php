<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPreference;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerService
{
    public function createCustomer(array $data): Customer
    {
        $customer = Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
        ]);

        // Create default preferences
        $customer->preferences()->create([
            'marketing_emails' => $data['marketing_emails'] ?? true,
            'order_notifications' => true,
        ]);

        // Send welcome email
        Mail::to($customer->email)->send(new \App\Mail\WelcomeCustomer($customer));

        return $customer;
    }

    public function updateCustomerProfile(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        // Update preferences if provided
        if (isset($data['preferences'])) {
            $customer->preferences()->updateOrCreate(
                ['customer_id' => $customer->id],
                $data['preferences']
            );
        }

        return $customer->fresh('preferences');
    }

    public function addCustomerAddress(Customer $customer, array $addressData, bool $setAsDefault = false): CustomerAddress
    {
        if ($setAsDefault) {
            // Remove default flag from other addresses of same type
            $customer->addresses()
                ->where('type', $addressData['type'])
                ->update(['is_default' => false]);
        }

        return $customer->addresses()->create(array_merge($addressData, [
            'is_default' => $setAsDefault
        ]));
    }

    public function getCustomerAnalytics(Customer $customer)
    {
        return [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => $customer->orders()->where('payment_status', 'paid')->avg('total_amount'),
            'favorite_products' => $this->getFavoriteProducts($customer),
            'last_order_date' => $customer->orders()->latest()->first()?->created_at,
            'customer_lifetime_value' => $this->calculateCustomerLifetimeValue($customer),
        ];
    }

    public function getCustomersWithFilters(array $filters = [])
    {
        $query = Customer::with(['preferences']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['has_orders'])) {
            $query->has('orders');
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    private function getFavoriteProducts(Customer $customer, $limit = 5)
    {
        return \App\Models\Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customer->id)
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit)
            ->get();
    }

    private function calculateCustomerLifetimeValue(Customer $customer)
    {
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');
        $daysSinceFirstOrder = $customer->orders()->oldest()->first()?->created_at?->diffInDays(now()) ?: 1;
        $averageOrderValue = $customer->orders()->where('payment_status', 'paid')->avg('total_amount') ?: 0;
        
        // Simple CLV calculation: (Average Order Value × Order Frequency × Gross Margin × Lifespan)
        $orderFrequency = $customer->orders()->count() / max($daysSinceFirstOrder / 365, 0.1);
        $grossMargin = 0.3; // 30% margin assumption
        $estimatedLifespan = 3; // 3 years assumption
        
        return $averageOrderValue * $orderFrequency * $grossMargin * $estimatedLifespan;
    }
}