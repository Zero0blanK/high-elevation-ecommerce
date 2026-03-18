<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPreference;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function createCustomer(array $data): Customer
    {
        $customer = $this->customerRepository->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
        ]);

        // Create default preferences
        $this->customerRepository->createPreferences($customer->id, [
            'marketing_emails' => $data['marketing_emails'] ?? true,
            'order_notifications' => true,
        ]);

        // Send welcome email
        Mail::to($customer->email)->send(new \App\Mail\WelcomeCustomer($customer));

        return $customer;
    }

    public function updateCustomerProfile(Customer $customer, array $data): Customer
    {
        $this->customerRepository->update($customer, $data);

        // Update preferences if provided
        if (isset($data['preferences'])) {
            $this->customerRepository->updateOrCreatePreferences(
                $customer->id,
                $data['preferences']
            );
        }

        return $customer->fresh('preferences');
    }

    public function addCustomerAddress(Customer $customer, array $addressData, bool $setAsDefault = false): CustomerAddress
    {
        if ($setAsDefault) {
            // Remove default flag from other addresses of same type
            $this->customerRepository->clearDefaultAddresses($customer->id, $addressData['type']);
        }

        return $this->customerRepository->createAddress(
            $customer->id,
            array_merge($addressData, ['is_default' => $setAsDefault])
        );
    }

    public function updateCustomerAddress(CustomerAddress $address, array $data): CustomerAddress
    {
        if (!empty($data['is_default']) && $data['is_default']) {
            $this->customerRepository->clearDefaultAddresses($address->customer_id, $address->type);
        }

        $this->customerRepository->updateAddress($address, $data);

        return $address->fresh();
    }

    public function deleteCustomerAddress(CustomerAddress $address): bool
    {
        return $this->customerRepository->deleteAddress($address);
    }

    public function getCustomerAddresses(int $customerId): \Illuminate\Support\Collection
    {
        return $this->customerRepository->getAddresses($customerId);
    }

    public function getCustomerAddressesByType(int $customerId, string $type): \Illuminate\Support\Collection
    {
        return $this->customerRepository->getAddressesByType($customerId, $type);
    }

    public function getCustomerAnalytics(Customer $customer): array
    {
        $stats = $this->customerRepository->getOrderStatistics($customer);

        return array_merge($stats, [
            'favorite_products' => $this->getFavoriteProducts($customer),
            'customer_lifetime_value' => $this->calculateCustomerLifetimeValue($customer),
        ]);
    }

    public function getCustomersWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->customerRepository->getCustomersWithFilters($filters);
    }

    public function findCustomerById(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function findCustomerByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findByEmail($email);
    }

    public function getCustomerPreferences(int $customerId): ?CustomerPreference
    {
        return $this->customerRepository->getPreferences($customerId);
    }

    public function updateCustomerPreferences(int $customerId, array $data): CustomerPreference
    {
        return $this->customerRepository->updateOrCreatePreferences($customerId, $data);
    }

    private function getFavoriteProducts(Customer $customer, int $limit = 5)
    {
        return Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customer->id)
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit)
            ->get();
    }

    private function calculateCustomerLifetimeValue(Customer $customer): float
    {
        $stats = $this->customerRepository->getOrderStatistics($customer);
        $totalSpent = $stats['total_spent'] ?? 0;
        $averageOrderValue = $stats['average_order_value'] ?? 0;
        
        $daysSinceFirstOrder = $customer->orders()->oldest()->first()?->created_at?->diffInDays(now()) ?: 1;
        
        // Simple CLV calculation: (Average Order Value × Order Frequency × Gross Margin × Lifespan)
        $orderFrequency = $stats['total_orders'] / max($daysSinceFirstOrder / 365, 0.1);
        $grossMargin = 0.3; // 30% margin assumption
        $estimatedLifespan = 3; // 3 years assumption
        
        return (float) ($averageOrderValue * $orderFrequency * $grossMargin * $estimatedLifespan);
    }
}