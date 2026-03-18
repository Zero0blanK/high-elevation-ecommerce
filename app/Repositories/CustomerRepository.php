<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPreference;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    protected Customer $customer;
    protected CustomerAddress $customerAddress;
    protected CustomerPreference $customerPreference;

    public function __construct(
        Customer $customer,
        CustomerAddress $customerAddress,
        CustomerPreference $customerPreference
    ) {
        $this->customer = $customer;
        $this->customerAddress = $customerAddress;
        $this->customerPreference = $customerPreference;
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Methods
    |--------------------------------------------------------------------------
    */

    public function findById(int $id): ?Customer
    {
        return $this->customer->newQuery()->find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->customer->newQuery()->where('email', $email)->first();
    }

    public function findWithRelations(int $id, array $relations = []): ?Customer
    {
        return $this->customer->newQuery()->with($relations)->find($id);
    }

    public function create(array $data): Customer
    {
        return $this->customer->newQuery()->create($data);
    }

    public function update(Customer $customer, array $data): bool
    {
        return $customer->update($data);
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }

    public function getCustomersWithFilters(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->customer->newQuery()->with(['preferences']);

        if (!empty($filters['search'])) {
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

        if (!empty($filters['has_orders'])) {
            $query->has('orders');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getCustomerOrders(int $customerId)
    {
        return $this->customer->newQuery()
            ->find($customerId)
            ?->orders()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrderStatistics(Customer $customer): array
    {
        return [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => $customer->orders()->where('payment_status', 'paid')->avg('total_amount'),
            'last_order_date' => $customer->orders()->latest()->first()?->created_at,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Address Methods
    |--------------------------------------------------------------------------
    */

    public function getAddresses(int $customerId): Collection
    {
        return $this->customerAddress->newQuery()
            ->where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->get();
    }

    public function getAddressesByType(int $customerId, string $type): Collection
    {
      return $this->customerAddress->newQuery()
        ->where('customer_id', $customerId)
        ->where(function ($query) use ($type) {
          $query->where('type', $type)
                ->orWhere('type', 'both');
        })
        ->orderBy('is_default', 'desc')
        ->get();
    }

    public function findAddress(int $addressId): ?CustomerAddress
    {
        return $this->customerAddress->newQuery()->find($addressId);
    }

    public function findAddressByCustomer(int $addressId, int $customerId): ?CustomerAddress
    {
        return $this->customerAddress->newQuery()
            ->where('id', $addressId)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createAddress(int $customerId, array $data): CustomerAddress
    {
        return $this->customerAddress->newQuery()->create(
            array_merge($data, ['customer_id' => $customerId])
        );
    }

    public function updateAddress(CustomerAddress $address, array $data): bool
    {
        return $address->update($data);
    }

    public function deleteAddress(CustomerAddress $address): bool
    {
        return $address->delete();
    }

    public function clearDefaultAddresses(int $customerId, string $type): int
    {
        return $this->customerAddress->newQuery()
            ->where('customer_id', $customerId)
            ->where('type', $type)
            ->update(['is_default' => false]);
    }

    public function getDefaultAddress(int $customerId, string $type): ?CustomerAddress
    {
        return $this->customerAddress->newQuery()
            ->where('customer_id', $customerId)
            ->where('type', $type)
            ->where('is_default', true)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Preference Methods
    |--------------------------------------------------------------------------
    */

    public function getPreferences(int $customerId): ?CustomerPreference
    {
        return $this->customerPreference->newQuery()
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createPreferences(int $customerId, array $data): CustomerPreference
    {
        return $this->customerPreference->newQuery()->create(
            array_merge($data, ['customer_id' => $customerId])
        );
    }

    public function updateOrCreatePreferences(int $customerId, array $data): CustomerPreference
    {
        return $this->customerPreference->newQuery()->updateOrCreate(
            ['customer_id' => $customerId],
            $data
        );
    }
}