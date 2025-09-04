<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\EmailService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;
    protected $emailService;

    public function __construct(CustomerService $customerService, EmailService $emailService)
    {
        $this->customerService = $customerService;
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'is_active' => $request->status === 'active' ? true : ($request->status === 'inactive' ? false : null),
            'has_orders' => $request->has_orders === 'yes'
        ];

        $customers = $this->customerService->getCustomersWithFilters($filters);
        
        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['addresses', 'preferences', 'orders.items.product']);
        $analytics = $this->customerService->getCustomerAnalytics($customer);
        
        return view('admin.customers.show', compact('customer', 'analytics'));
    }

    public function edit(Customer $customer)
    {
        $customer->load(['addresses', 'preferences']);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        try {
            $this->customerService->updateCustomerProfile($customer, $request->all());
            return back()->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        try {
            $this->emailService->sendBulkEmail(
                $request->customer_ids,
                $request->subject,
                $request->content
            );

            return back()->with('success', 'Emails sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending emails: ' . $e->getMessage());
        }
    }
}