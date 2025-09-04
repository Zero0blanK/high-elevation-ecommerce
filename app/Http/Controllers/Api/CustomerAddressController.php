<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderBy('is_default', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:billing,shipping',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'company' => 'nullable|string|max:100',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        try {
            $address = $this->customerService->addCustomerAddress(
                $request->user(),
                $request->all(),
                $request->boolean('is_default')
            );

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(CustomerAddress $address)
    {
        if ($address->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    public function update(Request $request, CustomerAddress $address)
    {
        if ($address->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $request->validate([
            'type' => 'required|in:billing,shipping',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'company' => 'nullable|string|max:100',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        try {
            // If setting as default, remove default from other addresses of same type
            if ($request->boolean('is_default')) {
                $request->user()->addresses()
                    ->where('type', $request->type)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(CustomerAddress $address)
    {
        if ($address->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        try {
            $address->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}