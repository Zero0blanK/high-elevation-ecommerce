<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'email_verified_at' => $this->email_verified_at,
            'addresses' => $this->addresses?->map(function ($address) {
                return [
                    'id' => $address->id,
                    'type' => $address->type,
                    'first_name' => $address->first_name,
                    'last_name' => $address->last_name,
                    'company' => $address->company,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'is_default' => $address->is_default,
                    'formatted_address' => $address->formatted_address,
                ];
            }),
            'preferences' => $this->preferences ? [
                'preferred_roast_level' => $this->preferences->preferred_roast_level,
                'preferred_grind_type' => $this->preferences->preferred_grind_type,
                'favorite_origins' => $this->preferences->favorite_origins,
                'marketing_emails' => $this->preferences->marketing_emails,
                'order_notifications' => $this->preferences->order_notifications,
            ] : null,
            'statistics' => [
                'total_orders' => $this->total_orders,
                'total_spent' => $this->total_spent,
            ],
            'created_at' => $this->created_at,
            'last_login_at' => $this->last_login_at,
        ];
    }
}