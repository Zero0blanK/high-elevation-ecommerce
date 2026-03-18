<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customerId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $items = $this->cartService->getCart($customerId, $sessionId);
        $totals = $this->cartService->getCartTotals($customerId, $sessionId);

        return response()->json([
            'items' => $items,
            'totals' => $totals,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_options' => 'nullable|array',
        ]);

        $customerId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $item = $this->cartService->addToCart(
            $request->product_id,
            $request->quantity,
            $request->product_options ?? [],
            $customerId,
            $sessionId
        );

        return response()->json(['message' => 'Item added to cart', 'item' => $item], 201);
    }

    public function update(Request $request, $itemId): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0']);

        $customerId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $this->cartService->updateCartItem($itemId, $request->quantity, $customerId, $sessionId);

        return response()->json(['message' => 'Cart updated']);
    }

    public function destroy(Request $request, $itemId): JsonResponse
    {
        $customerId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $this->cartService->removeFromCart($itemId, $customerId, $sessionId);

        return response()->json(['message' => 'Item removed']);
    }

    public function clear(Request $request): JsonResponse
    {
        $customerId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $this->cartService->clearCart($customerId, $sessionId);

        return response()->json(['message' => 'Cart cleared']);
    }
}
