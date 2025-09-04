<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $customerId = Auth::guard('customer')->id();
        $cartTotals = $this->cartService->getCartTotals($customerId);

        return view('cart.index', [
            'cartItems' => $cartTotals['items'],
            'total' => $cartTotals['subtotal']
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $customerId = Auth::guard('customer')->id();
            $options = $request->get('product_options', []);
            
            $this->cartService->addToCart(
                $request->product_id,
                $request->quantity,
                $options,
                $customerId
            );

            return back()->with('success', 'Product added to cart successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $customerId = Auth::guard('customer')->id();
            $quantity = (int) $request->input('quantity', 1);
            
            $this->cartService->updateCartItem(
                $id,
                $request->quantity,
                $customerId
            );

            return back()->with('success', 'Cart updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function remove($id)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $this->cartService->removeFromCart($id, $customerId);

            return back()->with('success', 'Item removed from cart!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function clear()
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $this->cartService->clearCart($customerId);

            return back()->with('success', 'Cart cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function count()
    {
        $customerId = Auth::guard('customer')->id();
        $cartTotals = $this->cartService->getCartTotals($customerId);
        
        return response()->json(['count' => $cartTotals['total_items']]);
    }

    public function getCartItems()
    {
        $customerId = Auth::guard('customer')->id();
        return $this->cartService->getCart($customerId);
    }

    public function increase($cartItemId)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $this->cartService->increaseQuantity($cartItemId, $customerId);
            
            return back()->with('success', 'Quantity increased!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function decrease($cartItemId)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $this->cartService->decreaseQuantity($cartItemId, $customerId);
            
            return back()->with('success', 'Quantity decreased!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function increaseAjax($cartItemId)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $this->cartService->increaseQuantity($cartItemId, $customerId);
            
            // Get updated cart totals
            $cartTotals = $this->cartService->getCartTotals($customerId);
            
            return response()->json([
                'success' => true,
                'message' => 'Quantity increased!',
                'cartTotals' => $cartTotals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function decreaseAjax($cartItemId)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $this->cartService->decreaseQuantity($cartItemId, $customerId);
            
            // Get updated cart totals
            $cartTotals = $this->cartService->getCartTotals($customerId);
            
            return response()->json([
                'success' => true,
                'message' => 'Quantity decreased!',
                'cartTotals' => $cartTotals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}