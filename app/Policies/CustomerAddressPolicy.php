<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\CustomerAddress;

class CustomerAddressPolicy
{
    public function view(Customer $customer, CustomerAddress $address): bool
    {
        return $customer->id === $address->customer_id;
    }

    public function update(Customer $customer, CustomerAddress $address): bool
    {
        return $customer->id === $address->customer_id;
    }

    public function delete(Customer $customer, CustomerAddress $address): bool
    {
        return $customer->id === $address->customer_id;
    }
}
