<?php

namespace App\Http\Controllers;

use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $wishlistService
    ) {}

    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $wishlistItems = $this->wishlistService->getWishlist($customer->id);

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $productId = $request->input('product_id');

        $result = $this->wishlistService->toggle($customer->id, $productId);

        if ($request->ajax()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function remove($productId)
    {
        $customer = Auth::guard('customer')->user();
        $this->wishlistService->remove($customer->id, $productId);

        return back()->with('success', 'Item removed from wishlist.');
    }
}
