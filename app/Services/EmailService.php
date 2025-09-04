<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendWelcomeEmail(Customer $customer)
    {
        try {
            Mail::to($customer->email)->send(new \App\Mail\WelcomeCustomer($customer));
            Log::info('Welcome email sent', ['customer_id' => $customer->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendAbandonedCartReminder($customerId = null, $sessionId = null)
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart($customerId, $sessionId);
        
        if ($cart->isEmpty()) {
            return;
        }

        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer && $customer->preferences->marketing_emails) {
                try {
                    Mail::to($customer->email)->send(new \App\Mail\AbandonedCart($customer, $cart));
                    Log::info('Abandoned cart email sent', ['customer_id' => $customer->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to send abandoned cart email', [
                        'customer_id' => $customer->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    public function sendBirthdayDiscounts()
    {
        $customers = Customer::whereMonth('date_of_birth', now()->month)
            ->whereDay('date_of_birth', now()->day)
            ->whereHas('preferences', function ($query) {
                $query->where('marketing_emails', true);
            })
            ->get();

        foreach ($customers as $customer) {
            try {
                // Create birthday coupon
                $coupon = \App\Models\Coupon::create([
                    'code' => 'BIRTHDAY' . $customer->id . now()->year,
                    'type' => 'percentage',
                    'value' => 15, // 15% discount
                    'minimum_amount' => 0,
                    'usage_limit' => 1,
                    'is_active' => true,
                    'starts_at' => now(),
                    'expires_at' => now()->addDays(30),
                ]);

                Mail::to($customer->email)->send(new \App\Mail\BirthdayDiscount($customer, $coupon));
                Log::info('Birthday discount email sent', ['customer_id' => $customer->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send birthday discount email', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function sendProductRecommendations(Customer $customer)
    {
        $customerService = app(CustomerService::class);
        $analytics = $customerService->getCustomerAnalytics($customer);
        
        if (empty($analytics['favorite_products'])) {
            return;
        }

        // Get similar products based on favorite categories
        $favoriteCategories = $analytics['favorite_products']->pluck('category_id')->unique();
        
        $recommendations = \App\Models\Product::whereIn('category_id', $favoriteCategories)
            ->whereNotIn('id', $analytics['favorite_products']->pluck('id'))
            ->active()
            ->inStock()
            ->limit(6)
            ->get();

        if ($recommendations->isNotEmpty() && $customer->preferences->marketing_emails) {
            try {
                Mail::to($customer->email)->send(new \App\Mail\ProductRecommendations($customer, $recommendations));
                Log::info('Product recommendations email sent', ['customer_id' => $customer->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send product recommendations email', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function sendReorderReminder(Customer $customer)
    {
        $lastOrder = $customer->orders()
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

        if (!$lastOrder || $lastOrder->created_at->diffInDays(now()) < 30) {
            return;
        }

        if ($customer->preferences->marketing_emails) {
            try {
                Mail::to($customer->email)->send(new \App\Mail\ReorderReminder($customer, $lastOrder));
                Log::info('Reorder reminder email sent', ['customer_id' => $customer->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send reorder reminder email', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function sendBulkEmail($customerIds, $subject, $content, $templateName = null)
    {
        $customers = Customer::whereIn('id', $customerIds)
            ->whereHas('preferences', function ($query) {
                $query->where('marketing_emails', true);
            })
            ->get();

        foreach ($customers as $customer) {
            try {
                Mail::to($customer->email)->send(new \App\Mail\BulkEmail($customer, $subject, $content, $templateName));
                Log::info('Bulk email sent', ['customer_id' => $customer->id, 'subject' => $subject]);
            } catch (\Exception $e) {
                Log::error('Failed to send bulk email', [
                    'customer_id' => $customer->id,
                    'subject' => $subject,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}