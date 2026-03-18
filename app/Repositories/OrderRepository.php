<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    protected Order $order;
    protected OrderItem $orderItem;
    protected OrderAddress $orderAddress;
    protected Payment $payment;

    public function __construct(
        Order $order,
        OrderItem $orderItem,
        OrderAddress $orderAddress,
        Payment $payment
    ) {
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->orderAddress = $orderAddress;
        $this->payment = $payment;
    }

    /*
    |--------------------------------------------------------------------------
    | Order Methods
    |--------------------------------------------------------------------------
    */

    public function findById(int $id): ?Order
    {
        return $this->order->newQuery()->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->order->newQuery()->where('order_number', $orderNumber)->first();
    }

    public function findWithRelations(int $id, array $relations = []): ?Order
    {
        return $this->order->newQuery()->with($relations)->find($id);
    }

    public function findByCustomer(int $orderId, int $customerId): ?Order
    {
        return $this->order->newQuery()
            ->where('id', $orderId)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function findByOrderNumberAndCustomer(string $orderNumber, int $customerId): ?Order
    {
        return $this->order->newQuery()
            ->where('order_number', $orderNumber)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function create(array $data): Order
    {
        return $this->order->newQuery()->create($data);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getOrdersByCustomer(int $customerId, array $relations = []): Collection
    {
        return $this->order->newQuery()
            ->with($relations)
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrdersByCustomerPaginated(int $customerId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->order->newQuery()
            ->with(['items.product'])
            ->where('customer_id', $customerId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('order_number', 'like', '%' . $filters['search'] . '%');
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function getOrdersWithFilters(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->order->newQuery()->with(['customer', 'items.product']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getOrderCountsByStatus(int $customerId): array
    {
        $orders = $this->order->newQuery()
            ->where('customer_id', $customerId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'all' => array_sum($orders),
            'pending' => $orders['pending'] ?? 0,
            'processing' => $orders['processing'] ?? 0,
            'shipped' => $orders['shipped'] ?? 0,
            'delivered' => $orders['delivered'] ?? 0,
            'cancelled' => $orders['cancelled'] ?? 0,
        ];
    }

    public function getPendingOrders(): Collection
    {
        return $this->order->newQuery()
            ->with(['customer', 'items'])
            ->pending()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getProcessingOrders(): Collection
    {
        return $this->order->newQuery()
            ->with(['customer', 'items'])
            ->processing()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Order Item Methods
    |--------------------------------------------------------------------------
    */

    public function createOrderItem(int $orderId, array $data): OrderItem
    {
        return $this->orderItem->newQuery()->create(
            array_merge($data, ['order_id' => $orderId])
        );
    }

    public function getOrderItems(int $orderId): Collection
    {
        return $this->orderItem->newQuery()
            ->with('product')
            ->where('order_id', $orderId)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Order Address Methods
    |--------------------------------------------------------------------------
    */

    public function createOrderAddress(int $orderId, int $addressId): OrderAddress
    {
        return $this->orderAddress->newQuery()->create([
            'order_id' => $orderId,
            'address_id' => $addressId
        ]);
    }

    public function getOrderAddresses(int $orderId): Collection
    {
        return $this->orderAddress->newQuery()
            ->with('address')
            ->where('order_id', $orderId)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    */

    public function createPayment(array $data): Payment
    {
        return $this->payment->newQuery()->create($data);
    }

    public function findPaymentById(int $id): ?Payment
    {
        return $this->payment->newQuery()->find($id);
    }

    public function findPaymentByTransactionId(string $transactionId): ?Payment
    {
        return $this->payment->newQuery()
            ->where('transaction_id', $transactionId)
            ->first();
    }

    public function updatePayment(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }

    public function getOrderPayments(int $orderId): Collection
    {
        return $this->payment->newQuery()
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSuccessfulPayment(int $orderId): ?Payment
    {
        return $this->payment->newQuery()
            ->where('order_id', $orderId)
            ->where('status', 'completed')
            ->first();
    }
}
