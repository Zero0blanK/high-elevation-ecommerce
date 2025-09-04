<?php

namespace App\Services;

use App\Models\ShoppingCart;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function addToCart($productId, $quantity = 1, $options = [], $customerId = null, $sessionId = null)
    {
        $product = Product::findOrFail($productId);
        
        if (!$product->isInStock()) {
            throw new \Exception('Product is out of stock');
        }
        
        if ($product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        $sessionId = $sessionId ?: Session::getId();
        
        // Check if item already exists in cart
        $cartItem = ShoppingCart::where('product_id', $productId)
            ->where(function ($query) use ($customerId, $sessionId) {
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->where('product_options', json_encode($options))
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->stock_quantity < $newQuantity) {
                throw new \Exception('Cannot add more items. Insufficient stock available');
            }
            $cartItem->update(['quantity' => $newQuantity]);
            return $cartItem;
        }

        return ShoppingCart::create([
            'session_id' => $sessionId,
            'customer_id' => $customerId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'product_options' => $options
        ]);
    }

    public function updateCartItem($cartItemId, $quantity, $customerId = null, $sessionId = null)
    {
        $cartItem = $this->getCartItem($cartItemId, $customerId, $sessionId);
        
        if ($quantity <= 0) {
            return $this->removeFromCart($cartItemId, $customerId, $sessionId);
        }

        if ($cartItem->product->stock_quantity < $quantity) {
            return back()->with('error', 'Not enough stock available');
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    public function removeFromCart($cartItemId, $customerId = null, $sessionId = null)
    {
        $cartItem = $this->getCartItem($cartItemId, $customerId, $sessionId);
        return $cartItem->delete();
    }

    public function getCart($customerId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $query = ShoppingCart::with(['product.primaryImage', 'product.category']);
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->get();
    }

    public function getCartTotals($customerId = null, $sessionId = null)
    {
        $cartItems = $this->getCart($customerId, $sessionId);
        
        $subtotal = 0;
        $totalItems = 0;
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            // Calculate price (sale price if on sale, otherwise regular price)
            $price = $item->product->is_on_sale ? $item->product->sale_price : $item->product->price;
            $itemTotal = $price * $item->quantity;
            
            $subtotal += $itemTotal;
            $totalItems += $item->quantity;
            $totalWeight += ($item->product->weight ?? 0) * $item->quantity;
            
            // Add the calculated total to the item for display
            $item->total_price = $itemTotal;
        }

        return [
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
            'total_weight' => $totalWeight,
            'items' => $cartItems
        ];
    }

    public function clearCart($customerId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $query = ShoppingCart::query();
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->delete();
    }

    public function mergeSessionCartToCustomer(Customer $customer, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $sessionCartItems = ShoppingCart::where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->get();

        foreach ($sessionCartItems as $sessionItem) {
            $existingItem = ShoppingCart::where('customer_id', $customer->id)
                ->where('product_id', $sessionItem->product_id)
                ->where('product_options', $sessionItem->product_options)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $sessionItem->quantity;
                $existingItem->save();
                $sessionItem->delete();
            } else {
                $sessionItem->update([
                    'customer_id' => $customer->id,
                    'session_id' => null
                ]);
            }
        }
    }

    public function applyCoupon($couponCode, $customerId = null, $sessionId = null)
    {
        $coupon = \App\Models\Coupon::where('code', $couponCode)->first();
        
        if (!$coupon) {
            throw new \Exception('Invalid coupon code');
        }

        $cartTotals = $this->getCartTotals($customerId, $sessionId);
        
        if (!$coupon->isValid($cartTotals['subtotal'])) {
            throw new \Exception('Coupon is not valid or has expired');
        }

        $discount = $coupon->calculateDiscount($cartTotals['subtotal']);
        
        return [
            'coupon' => $coupon,
            'discount_amount' => $discount,
            'new_total' => $cartTotals['subtotal'] - $discount
        ];
    }

    private function getCartItem($cartItemId, $customerId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $query = ShoppingCart::where('id', $cartItemId);
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItem = $query->first();
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found');
        }

        return $cartItem;
    }
    
    public function transferGuestCart($customerId, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        // Get guest cart items
        $guestCartItems = ShoppingCart::where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->get();

        foreach ($guestCartItems as $guestItem) {
            // Check if customer already has this product in cart
            $existingItem = ShoppingCart::where('customer_id', $customerId)
                ->where('product_id', $guestItem->product_id)
                ->where('product_options', $guestItem->product_options)
                ->first();

            if ($existingItem) {
                // Merge quantities
                $newQuantity = $existingItem->quantity + $guestItem->quantity;
                
                // Check stock availability
                if ($guestItem->product->stock_quantity >= $newQuantity) {
                    $existingItem->update(['quantity' => $newQuantity]);
                } else {
                    // If not enough stock, just keep the existing quantity
                    $existingItem->update(['quantity' => min($newQuantity, $guestItem->product->stock_quantity)]);
                }
                
                $guestItem->delete();
            } else {
                // Transfer to customer
                $guestItem->update([
                    'customer_id' => $customerId,
                    'session_id' => null
                ]);
            }
        }

        return true;
    }

    public function increaseQuantity($cartItemId, $customerId = null, $sessionId = null)
    {
        $cartItem = $this->getCartItem($cartItemId, $customerId, $sessionId);
        
        if ($cartItem->quantity >= $cartItem->product->stock_quantity) {
            throw new \Exception('Cannot add more items. Stock limit reached.');
        }
        
        $cartItem->increment('quantity');
        return $cartItem;
    }

    public function decreaseQuantity($cartItemId, $customerId = null, $sessionId = null)
    {
        $cartItem = $this->getCartItem($cartItemId, $customerId, $sessionId);
        
        if ($cartItem->quantity <= 1) {
            // If quantity is 1 or less, remove the item completely
            return $this->removeFromCart($cartItemId, $customerId, $sessionId);
        }
        
        $cartItem->decrement('quantity');
        return $cartItem;
    }
}