<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartService
{
    protected CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

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
        $cartItem = $this->cartRepository->findExistingCartItem(
            $productId,
            $customerId,
            $sessionId,
            $options
        );

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->stock_quantity < $newQuantity) {
                throw new \Exception('Cannot add more items. Insufficient stock available');
            }
            $this->cartRepository->update($cartItem, ['quantity' => $newQuantity]);
            return $cartItem->fresh();
        }

        return $this->cartRepository->create([
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
            throw new \Exception('Not enough stock available');
        }

        $this->cartRepository->update($cartItem, ['quantity' => $quantity]);
        return $cartItem->fresh();
    }

    public function removeFromCart($cartItemId, $customerId = null, $sessionId = null)
    {
        $cartItem = $this->getCartItem($cartItemId, $customerId, $sessionId);
        return $this->cartRepository->delete($cartItem);
    }

    public function getCart($customerId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        return $this->cartRepository->getCartWithProducts($customerId, $sessionId);
    }

    public function getCartTotals($customerId = null, $sessionId = null)
    {
        $cartItems = $this->getCart($customerId, $sessionId);
        
        $subtotal = 0;
        $totalItems = 0;
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $price = $item->product->is_on_sale ? $item->product->sale_price : $item->product->price;
            $itemTotal = $price * $item->quantity;
            
            $subtotal += $itemTotal;
            $totalItems += $item->quantity;
            $totalWeight += ($item->product->weight ?? 0) * $item->quantity;
            
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
        return $this->cartRepository->clearCart($customerId, $sessionId);
    }

    public function mergeSessionCartToCustomer(Customer $customer, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $sessionCartItems = $this->cartRepository->getGuestCartItems($sessionId);
        
        foreach ($sessionCartItems as $sessionItem) {
            try {
                $existingItem = $this->cartRepository->findCustomerCartItem(
                    $customer->id,
                    $sessionItem->product_id,
                    $sessionItem->product_options
                );

                if ($existingItem) {
                    $newQuantity = $existingItem->quantity + $sessionItem->quantity;
                    
                    if ($sessionItem->product->stock_quantity >= $newQuantity) {
                        $this->cartRepository->update($existingItem, ['quantity' => $newQuantity]);
                    } else {
                        $this->cartRepository->update($existingItem, [
                            'quantity' => min($newQuantity, $sessionItem->product->stock_quantity)
                        ]);
                    }
                    
                    $this->cartRepository->delete($sessionItem);
                } else {
                    $this->cartRepository->update($sessionItem, [
                        'customer_id' => $customer->id,
                        'session_id' => null
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error merging cart item: ' . $e->getMessage(), [
                    'session_item_id' => $sessionItem->id,
                    'customer_id' => $customer->id
                ]);
                continue;
            }
        }

        return true;
    }

    public function applyCoupon($couponCode, $customerId = null, $sessionId = null)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
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

    public function transferGuestCart($customerId, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $guestCartItems = $this->cartRepository->getGuestCartItems($sessionId);

        foreach ($guestCartItems as $guestItem) {
            $existingItem = $this->cartRepository->findCustomerCartItem(
                $customerId,
                $guestItem->product_id,
                $guestItem->product_options
            );

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $guestItem->quantity;
                
                if ($guestItem->product->stock_quantity >= $newQuantity) {
                    $this->cartRepository->update($existingItem, ['quantity' => $newQuantity]);
                } else {
                    $this->cartRepository->update($existingItem, [
                        'quantity' => min($newQuantity, $guestItem->product->stock_quantity)
                    ]);
                }
                
                $this->cartRepository->delete($guestItem);
            } else {
                $this->cartRepository->update($guestItem, [
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
        
        $this->cartRepository->incrementQuantity($cartItem);
        return $cartItem->fresh();
    }

    public function decreaseQuantity($cartItemId, $customerId = null, $sessionId = null)
    {
        $cartItem = $this->getCartItem($cartItemId, $customerId, $sessionId);
        
        if ($cartItem->quantity <= 1) {
            return $this->removeFromCart($cartItemId, $customerId, $sessionId);
        }
        
        $this->cartRepository->decrementQuantity($cartItem);
        return $cartItem->fresh();
    }

    private function getCartItem($cartItemId, $customerId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $cartItem = $this->cartRepository->findCartItem($cartItemId, $customerId, $sessionId);
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found');
        }

        return $cartItem;
    }
}