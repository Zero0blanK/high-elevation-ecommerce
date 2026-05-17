<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MultiSheetReportExport;
use App\Exports\StyledReportExport;
use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        return view('admin.analytics.index');
    }

    public function salesReport(Request $request)
    {
        $report = null;

        if ($request->filled(['period_start', 'period_end'])) {
            $request->validate([
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
            ]);

            try {
                $report = $this->analyticsService->generateSalesReport(
                    $request->period_start,
                    $request->period_end
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Error generating sales report: ' . $e->getMessage());
            }
        }

        return view('admin.analytics.sales', compact('report'));
    }

    public function customerReport(Request $request)
    {
        $report = null;

        if ($request->filled(['period_start', 'period_end'])) {
            $request->validate([
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
            ]);

            try {
                $report = $this->analyticsService->generateCustomerReport(
                    $request->period_start,
                    $request->period_end
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Error generating customer report: ' . $e->getMessage());
            }
        }

        return view('admin.analytics.customers', compact('report'));
    }

    public function inventoryReport()
    {
        try {
            $report = $this->analyticsService->generateInventoryReport();
            return view('admin.analytics.inventory', compact('report'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating inventory report: ' . $e->getMessage());
        }
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sales,customers,inventory,all',
            'format' => 'required|in:csv,xlsx',
            'period_start' => 'required_unless:type,inventory|date',
            'period_end' => 'required_unless:type,inventory|date|after_or_equal:period_start',
        ]);

        try {
            $exportedAt = now()->utc();
            $periodStart = $request->filled('period_start') ? Carbon::parse($request->period_start)->startOfDay() : null;
            $periodEnd = $request->filled('period_end') ? Carbon::parse($request->period_end)->endOfDay() : null;

            if ($request->type === 'sales') {
                $columns = $this->salesColumns();
                $filenameBase = 'sales_report_' . now()->format('Y-m-d_His');

                if ($request->format === 'xlsx') {
                    $rows = iterator_to_array($this->iterateSalesRows($periodStart, $periodEnd), false);
                    return $this->downloadStyledXlsx($filenameBase, 'Sales Report', $columns, $rows);
                }

                return $this->streamFlatCsv($filenameBase . '.csv', $columns, function ($handle, $cols) use ($periodStart, $periodEnd) {
                    foreach ($this->iterateSalesRows($periodStart, $periodEnd) as $row) {
                        $this->writeFlatRow($handle, $cols, $row);
                    }
                });
            }

            if ($request->type === 'customers') {
                $columns = $this->customerColumns();
                $filenameBase = 'customers_report_' . now()->format('Y-m-d_His');

                if ($request->format === 'xlsx') {
                    $rows = iterator_to_array($this->iterateCustomerRows($exportedAt, $periodStart, $periodEnd), false);
                    return $this->downloadStyledXlsx($filenameBase, 'Customer Report', $columns, $rows);
                }

                return $this->streamFlatCsv($filenameBase . '.csv', $columns, function ($handle, $cols) use ($exportedAt, $periodStart, $periodEnd) {
                    foreach ($this->iterateCustomerRows($exportedAt, $periodStart, $periodEnd) as $row) {
                        $this->writeFlatRow($handle, $cols, $row);
                    }
                });
            }

            if ($request->type === 'inventory') {
                $columns = $this->inventoryColumns();
                $filenameBase = 'inventory_report_' . now()->format('Y-m-d_His');

                if ($request->format === 'xlsx') {
                    $rows = iterator_to_array($this->iterateInventoryRows($exportedAt), false);
                    return $this->downloadStyledXlsx($filenameBase, 'Inventory Report', $columns, $rows);
                }

                return $this->streamFlatCsv($filenameBase . '.csv', $columns, function ($handle, $cols) use ($exportedAt) {
                    foreach ($this->iterateInventoryRows($exportedAt) as $row) {
                        $this->writeFlatRow($handle, $cols, $row);
                    }
                });
            }

            $salesColumns = $this->salesColumns();
            $inventoryColumns = $this->inventoryColumns();
            $customerColumns = $this->customerColumns();
            $allColumns = array_values(array_unique(array_merge(['report_type'], $salesColumns, $inventoryColumns, $customerColumns)));
            $filenameBase = 'all_reports_' . now()->format('Y-m-d_His');

            if ($request->format === 'xlsx') {
                $salesRows = iterator_to_array($this->iterateSalesRows($periodStart, $periodEnd), false);
                $inventoryRows = iterator_to_array($this->iterateInventoryRows($exportedAt), false);
                $customerRows = iterator_to_array($this->iterateCustomerRows($exportedAt, $periodStart, $periodEnd), false);

                return $this->downloadMultiSheetXlsx($filenameBase, [
                    ['title' => 'Sales Report', 'columns' => $salesColumns, 'rows' => $salesRows],
                    ['title' => 'Inventory Report', 'columns' => $inventoryColumns, 'rows' => $inventoryRows],
                    ['title' => 'Customer Report', 'columns' => $customerColumns, 'rows' => $customerRows],
                ]);
            }

            return $this->streamFlatCsv($filenameBase . '.csv', $allColumns, function ($handle, $cols) use ($exportedAt, $periodStart, $periodEnd) {
                foreach ($this->iterateSalesRows($periodStart, $periodEnd) as $row) {
                    $this->writeFlatRow($handle, $cols, array_merge(['report_type' => 'sales'], $row));
                }

                foreach ($this->iterateInventoryRows($exportedAt) as $row) {
                    $this->writeFlatRow($handle, $cols, array_merge(['report_type' => 'inventory'], $row));
                }

                foreach ($this->iterateCustomerRows($exportedAt, $periodStart, $periodEnd) as $row) {
                    $this->writeFlatRow($handle, $cols, array_merge(['report_type' => 'customers'], $row));
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    private function streamFlatCsv(string $filename, array $columns, callable $writer)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($columns, $writer) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            $writer($handle, $columns);
            fclose($handle);
        }, 200, $headers);
    }

    private function downloadStyledXlsx(string $filenameBase, string $sheetTitle, array $columns, array $rows)
    {
        return Excel::download(
            new StyledReportExport($sheetTitle, $columns, $rows),
            $filenameBase . '.xlsx'
        );
    }

    private function downloadMultiSheetXlsx(string $filenameBase, array $sheets)
    {
        return Excel::download(
            new MultiSheetReportExport($sheets),
            $filenameBase . '.xlsx'
        );
    }

    private function writeFlatRow($handle, array $columns, array $row): void
    {
        $normalized = [];
        foreach ($columns as $column) {
            $value = $row[$column] ?? '';
            $normalized[] = $value === null ? '' : $value;
        }
        fputcsv($handle, $normalized);
    }

    private function salesColumns(): array
    {
        return [
            'order_id',
            'order_number',
            'order_line_id',
            'order_created_at',
            'paid_at',
            'order_status',
            'payment_status',
            'payment_method',
            'shipping_method',
            'customer_id',
            'customer_email',
            'customer_name',
            'customer_country_code',
            'product_id',
            'sku',
            'product_name',
            'category_name',
            'quantity',
            'currency_code',
            'unit_price',
            'discount_amount',
            'line_subtotal_amount',
            'tax_amount',
            'shipping_amount',
            'line_total_amount',
            'unit_cost',
            'line_cogs_amount',
            'gross_margin_amount',
        ];
    }

    private function inventoryColumns(): array
    {
        return [
            'product_id',
            'sku',
            'product_name',
            'category_name',
            'currency_code',
            'on_hand_qty',
            'reserved_qty',
            'available_qty',
            'reorder_point_qty',
            'days_of_cover',
            'stock_status',
            'unit_cost',
            'on_hand_value',
            'available_value',
            'last_receipt_at',
            'last_issue_at',
            'inventory_turnover_30d',
        ];
    }

    private function customerColumns(): array
    {
        return [
            'customer_id',
            'email',
            'phone_e164',
            'customer_name',
            'country_code',
            'region',
            'city',
            'postal_code',
            'signup_at',
            'first_purchase_at',
            'last_purchase_at',
            'customer_tenure_days',
            'signup_cohort_month',
            'first_purchase_cohort_month',
            'currency_code',
            'lifetime_orders',
            'lifetime_units',
            'lifetime_gross_revenue',
            'lifetime_discount_amount',
            'lifetime_refund_amount',
            'lifetime_net_revenue',
            'lifetime_cogs',
            'lifetime_gross_margin',
            'ltv_amount',
            'aov_amount',
            'recency_days',
            'purchase_frequency_12m',
            'last_90d_orders',
            'last_90d_net_revenue',
            'products_purchased_count',
            'categories_purchased_count',
            'return_rate_12m',
            'email_opt_in',
            'sms_opt_in',
            'loyalty_tier',
            'churn_risk_score',
        ];
    }

    private function iterateSalesRows(?Carbon $periodStart, ?Carbon $periodEnd): \Generator
    {
        $start = $periodStart ?? now()->startOfMonth();
        $end = $periodEnd ?? now()->endOfMonth();
        $emittedOrderLineIds = [];

        $paidAtSubQuery = DB::table('payments')
            ->selectRaw('order_id, MAX(processed_at) as paid_at_utc')
            ->where('status', 'completed')
            ->groupBy('order_id');

        $rows = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoinSub($paidAtSubQuery, 'pay', function ($join) {
                $join->on('pay.order_id', '=', 'o.id');
            })
            ->leftJoin('customers as c', 'c.id', '=', 'o.customer_id')
            ->leftJoin('customer_addresses as ca', 'ca.id', '=', 'o.customer_address_id')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->whereBetween('o.created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->whereIn('o.payment_status', ['paid', 'refunded'])
            ->orderBy('o.id')
            ->orderBy('oi.id')
            ->select(
                'o.id as order_id',
                'o.order_number',
                'oi.id as order_line_id',
                'o.created_at as order_created_at',
                'pay.paid_at_utc',
                'o.status as order_status',
                'o.payment_status',
                'o.payment_method',
                'o.shipping_method',
                'c.id as customer_id',
                'c.email as customer_email',
                'c.first_name as customer_first_name',
                'c.last_name as customer_last_name',
                'ca.country as customer_country_code',
                'oi.product_id',
                'oi.product_sku as sku',
                'oi.product_name',
                'cat.name as category_name',
                'oi.quantity',
                'o.currency as currency_code',
                'oi.unit_price',
                'o.discount_amount',
                'o.tax_amount',
                'o.shipping_amount',
                'o.subtotal',
                'oi.total_price as line_subtotal_amount',
                'p.cost_price as unit_cost'
            )
            ->cursor();

        foreach ($rows as $row) {
            $orderLineId = (int) $row->order_line_id;
            if (isset($emittedOrderLineIds[$orderLineId])) {
                continue;
            }
            $emittedOrderLineIds[$orderLineId] = true;

            $lineSubtotal = (float) ($row->line_subtotal_amount ?? 0);
            $orderSubtotal = (float) ($row->subtotal ?? 0);
            $lineRatio = $orderSubtotal > 0 ? ($lineSubtotal / $orderSubtotal) : 0;

            $discountAmount = round(((float) ($row->discount_amount ?? 0)) * $lineRatio, 2);
            $taxAmount = round(((float) ($row->tax_amount ?? 0)) * $lineRatio, 2);
            $shippingAmount = round(((float) ($row->shipping_amount ?? 0)) * $lineRatio, 2);
            $lineTotal = round($lineSubtotal - $discountAmount + $taxAmount + $shippingAmount, 2);
            $unitCost = (float) ($row->unit_cost ?? 0);
            $lineCogs = round($unitCost * (int) $row->quantity, 2);
            $grossMargin = round($lineTotal - $lineCogs, 2);

            yield [
                'order_id' => $row->order_id,
                'order_number' => $row->order_number,
                'order_line_id' => $row->order_line_id,
                'order_created_at' => $this->formatDateTime($row->order_created_at),
                'paid_at' => $this->formatDateTime($row->paid_at_utc),
                'order_status' => $row->order_status,
                'payment_status' => $row->payment_status,
                'payment_method' => $row->payment_method,
                'shipping_method' => $row->shipping_method,
                'customer_id' => $row->customer_id,
                'customer_email' => $row->customer_email,
                'customer_name' => trim(($row->customer_first_name ?? '') . ' ' . ($row->customer_last_name ?? '')),
                'customer_country_code' => $row->customer_country_code,
                'product_id' => $row->product_id,
                'sku' => $row->sku,
                'product_name' => $row->product_name,
                'category_name' => $row->category_name,
                'quantity' => (int) $row->quantity,
                'currency_code' => $row->currency_code,
                'unit_price' => round((float) ($row->unit_price ?? 0), 2),
                'discount_amount' => $discountAmount,
                'line_subtotal_amount' => round($lineSubtotal, 2),
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'line_total_amount' => $lineTotal,
                'unit_cost' => round($unitCost, 2),
                'line_cogs_amount' => $lineCogs,
                'gross_margin_amount' => $grossMargin,
            ];
        }
    }

    private function iterateInventoryRows(Carbon $exportedAt): \Generator
    {
        $thirtyDaysAgo = $exportedAt->copy()->subDays(30);

        $reservedQtyByProduct = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->whereIn('o.status', ['pending', 'processing'])
            ->selectRaw('oi.product_id as product_id, SUM(oi.quantity) as reserved_qty')
            ->groupBy('oi.product_id')
            ->pluck('reserved_qty', 'product_id');

        $soldQty30ByProduct = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('o.payment_status', 'paid')
            ->whereBetween('o.created_at', [$thirtyDaysAgo->toDateTimeString(), $exportedAt->toDateTimeString()])
            ->selectRaw('oi.product_id as product_id, SUM(oi.quantity) as sold_qty_30d')
            ->groupBy('oi.product_id')
            ->pluck('sold_qty_30d', 'product_id');

        $lastReceiptByProduct = DB::table('inventory_logs')
            ->where('quantity_changed', '>', 0)
            ->selectRaw('product_id, MAX(created_at) as last_receipt_at')
            ->groupBy('product_id')
            ->pluck('last_receipt_at', 'product_id');

        $lastIssueByProduct = DB::table('inventory_logs')
            ->where('quantity_changed', '<', 0)
            ->selectRaw('product_id, MAX(created_at) as last_issue_at')
            ->groupBy('product_id')
            ->pluck('last_issue_at', 'product_id');

        $products = DB::table('products as p')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->where('p.is_active', true)
            ->orderBy('p.id')
            ->select(
                'p.id as product_id',
                'p.sku',
                'p.name as product_name',
                'cat.name as category_name',
                'p.stock_quantity',
                'p.low_stock_threshold',
                'p.cost_price'
            )
            ->cursor();

        foreach ($products as $product) {
            $onHandQty = (int) ($product->stock_quantity ?? 0);
            $reservedQty = (int) ($reservedQtyByProduct[$product->product_id] ?? 0);
            $availableQty = $onHandQty - $reservedQty;
            $reorderPointQty = (int) ($product->low_stock_threshold ?? 0);
            $soldQty30 = (float) ($soldQty30ByProduct[$product->product_id] ?? 0);
            $avgDailySold = $soldQty30 / 30;
            $daysOfCover = $avgDailySold > 0 ? round(max($availableQty, 0) / $avgDailySold, 2) : null;
            $unitCost = round((float) ($product->cost_price ?? 0), 2);
            $onHandValue = round($onHandQty * $unitCost, 2);
            $availableValue = round(max($availableQty, 0) * $unitCost, 2);
            $turnover30d = $onHandQty > 0 ? round($soldQty30 / $onHandQty, 4) : null;

            if ($onHandQty <= 0) {
                $stockStatus = 'out_of_stock';
            } elseif ($onHandQty <= $reorderPointQty) {
                $stockStatus = 'low_stock';
            } else {
                $stockStatus = 'in_stock';
            }

            yield [
                'product_id' => $product->product_id,
                'sku' => $product->sku,
                'product_name' => $product->product_name,
                'category_name' => $product->category_name,
                'currency_code' => 'PHP',
                'on_hand_qty' => $onHandQty,
                'reserved_qty' => $reservedQty,
                'available_qty' => $availableQty,
                'reorder_point_qty' => $reorderPointQty,
                'days_of_cover' => $daysOfCover,
                'stock_status' => $stockStatus,
                'unit_cost' => $unitCost,
                'on_hand_value' => $onHandValue,
                'available_value' => $availableValue,
                'last_receipt_at' => $this->formatDateTime($lastReceiptByProduct[$product->product_id] ?? null),
                'last_issue_at' => $this->formatDateTime($lastIssueByProduct[$product->product_id] ?? null),
                'inventory_turnover_30d' => $turnover30d,
            ];
        }
    }

    private function iterateCustomerRows(Carbon $exportedAt, ?Carbon $periodStart, ?Carbon $periodEnd): \Generator
    {
        $windowStart = $periodStart ?? now()->startOfMonth();
        $windowEnd = $periodEnd ?? now()->endOfMonth();
        $last90Start = $exportedAt->copy()->subDays(90);
        $last12MonthsStart = $exportedAt->copy()->subMonths(12);

        $paidOrdersByCustomer = DB::table('orders')
            ->where('payment_status', 'paid')
            ->selectRaw('customer_id, MIN(created_at) as first_purchase_at, MAX(created_at) as last_purchase_at, COUNT(*) as lifetime_orders, SUM(subtotal) as lifetime_gross_revenue, SUM(discount_amount) as lifetime_discount_amount, SUM(total_amount) as lifetime_net_revenue')
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        $lifetimeUnitsByCustomer = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('o.payment_status', 'paid')
            ->selectRaw('o.customer_id as customer_id, SUM(oi.quantity) as lifetime_units')
            ->groupBy('o.customer_id')
            ->pluck('lifetime_units', 'customer_id');

        $lifetimeCogsByCustomer = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->where('o.payment_status', 'paid')
            ->selectRaw('o.customer_id as customer_id, SUM(oi.quantity * COALESCE(p.cost_price, 0)) as lifetime_cogs')
            ->groupBy('o.customer_id')
            ->pluck('lifetime_cogs', 'customer_id');

        $refundAmountByCustomer = DB::table('orders')
            ->where('payment_status', 'refunded')
            ->selectRaw('customer_id, SUM(total_amount) as lifetime_refund_amount')
            ->groupBy('customer_id')
            ->pluck('lifetime_refund_amount', 'customer_id');

        $orders12MonthsByCustomer = DB::table('orders')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$last12MonthsStart->toDateTimeString(), $exportedAt->toDateTimeString()])
            ->selectRaw('customer_id, COUNT(*) as orders_12m')
            ->groupBy('customer_id')
            ->pluck('orders_12m', 'customer_id');

        $refundedOrders12MonthsByCustomer = DB::table('orders')
            ->where('payment_status', 'refunded')
            ->whereBetween('created_at', [$last12MonthsStart->toDateTimeString(), $exportedAt->toDateTimeString()])
            ->selectRaw('customer_id, COUNT(*) as refunded_orders_12m')
            ->groupBy('customer_id')
            ->pluck('refunded_orders_12m', 'customer_id');

        $orders90DaysByCustomer = DB::table('orders')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$last90Start->toDateTimeString(), $exportedAt->toDateTimeString()])
            ->selectRaw('customer_id, COUNT(*) as orders_90d, SUM(total_amount) as net_revenue_90d')
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        $productAndCategoryCounts = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->where('o.payment_status', 'paid')
            ->selectRaw('o.customer_id as customer_id, COUNT(DISTINCT oi.product_id) as products_count, COUNT(DISTINCT p.category_id) as categories_count')
            ->groupBy('o.customer_id')
            ->get()
            ->keyBy('customer_id');

        $addressesByCustomer = DB::table('customer_addresses')
            ->select('customer_id', 'country', 'state', 'city', 'postal_code', 'is_default')
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get()
            ->groupBy('customer_id')
            ->map(function ($rows) {
                return $rows->first();
            });

        $preferencesByCustomer = DB::table('customer_preferences')
            ->select('customer_id', 'marketing_emails', 'order_notifications')
            ->get()
            ->keyBy('customer_id');

        $activeCustomerIds = DB::table('orders')
            ->whereBetween('created_at', [$windowStart->toDateTimeString(), $windowEnd->toDateTimeString()])
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $customers = DB::table('customers')
            ->where(function ($query) use ($windowStart, $windowEnd, $activeCustomerIds) {
                $query->whereBetween('created_at', [$windowStart->toDateTimeString(), $windowEnd->toDateTimeString()])
                    ->orWhereIn('id', $activeCustomerIds);
            })
            ->orderBy('id')
            ->cursor();

        foreach ($customers as $customer) {
            $paid = $paidOrdersByCustomer->get($customer->id);
            $address = $addressesByCustomer->get($customer->id);
            $preferences = $preferencesByCustomer->get($customer->id);
            $orders90d = $orders90DaysByCustomer->get($customer->id);
            $distinctCounts = $productAndCategoryCounts->get($customer->id);

            $lifetimeOrders = (int) ($paid->lifetime_orders ?? 0);
            $lifetimeUnits = (int) ($lifetimeUnitsByCustomer[$customer->id] ?? 0);
            $lifetimeGrossRevenue = round((float) ($paid->lifetime_gross_revenue ?? 0), 2);
            $lifetimeDiscount = round((float) ($paid->lifetime_discount_amount ?? 0), 2);
            $lifetimeRefund = round((float) ($refundAmountByCustomer[$customer->id] ?? 0), 2);
            $lifetimeNetRevenue = round((float) ($paid->lifetime_net_revenue ?? 0), 2);
            $lifetimeCogs = round((float) ($lifetimeCogsByCustomer[$customer->id] ?? 0), 2);
            $lifetimeGrossMargin = round($lifetimeNetRevenue - $lifetimeCogs, 2);
            $ltv = $lifetimeNetRevenue;
            $aov = $lifetimeOrders > 0 ? round($lifetimeNetRevenue / $lifetimeOrders, 2) : 0;

            $signupAt = Carbon::parse($customer->created_at);
            $firstPurchaseAt = $paid?->first_purchase_at ? Carbon::parse($paid->first_purchase_at) : null;
            $lastPurchaseAt = $paid?->last_purchase_at ? Carbon::parse($paid->last_purchase_at) : null;
            $recencyDays = $lastPurchaseAt ? $lastPurchaseAt->diffInDays($exportedAt) : null;
            $orders12m = (int) ($orders12MonthsByCustomer[$customer->id] ?? 0);
            $purchaseFrequency12m = round($orders12m / 12, 4);
            $refundedOrders12m = (int) ($refundedOrders12MonthsByCustomer[$customer->id] ?? 0);
            $returnRate12m = $orders12m > 0 ? round($refundedOrders12m / $orders12m, 4) : 0;
            $last90dOrders = (int) ($orders90d->orders_90d ?? 0);
            $last90dNetRevenue = round((float) ($orders90d->net_revenue_90d ?? 0), 2);

            if ($lifetimeNetRevenue >= 50000) {
                $loyaltyTier = 'platinum';
            } elseif ($lifetimeNetRevenue >= 20000) {
                $loyaltyTier = 'gold';
            } elseif ($lifetimeNetRevenue >= 10000) {
                $loyaltyTier = 'silver';
            } else {
                $loyaltyTier = 'bronze';
            }

            if ($lifetimeOrders === 0 || $recencyDays === null) {
                $churnRiskScore = 1.0000;
            } else {
                $recencyFactor = min($recencyDays / 180, 1) * 0.7;
                $frequencyFactor = (1 - min($purchaseFrequency12m / 1.5, 1)) * 0.3;
                $churnRiskScore = round(min(1, max(0, $recencyFactor + $frequencyFactor)), 4);
            }

            yield [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'phone_e164' => $customer->phone,
                'customer_name' => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                'country_code' => $address->country ?? null,
                'region' => $address->state ?? null,
                'city' => $address->city ?? null,
                'postal_code' => $address->postal_code ?? null,
                'signup_at' => $this->formatDateTime($signupAt),
                'first_purchase_at' => $this->formatDateTime($firstPurchaseAt),
                'last_purchase_at' => $this->formatDateTime($lastPurchaseAt),
                'customer_tenure_days' => $signupAt->diffInDays($exportedAt),
                'signup_cohort_month' => $signupAt->format('Y-m'),
                'first_purchase_cohort_month' => $firstPurchaseAt?->format('Y-m'),
                'currency_code' => 'PHP',
                'lifetime_orders' => $lifetimeOrders,
                'lifetime_units' => $lifetimeUnits,
                'lifetime_gross_revenue' => $lifetimeGrossRevenue,
                'lifetime_discount_amount' => $lifetimeDiscount,
                'lifetime_refund_amount' => $lifetimeRefund,
                'lifetime_net_revenue' => $lifetimeNetRevenue,
                'lifetime_cogs' => $lifetimeCogs,
                'lifetime_gross_margin' => $lifetimeGrossMargin,
                'ltv_amount' => $ltv,
                'aov_amount' => $aov,
                'recency_days' => $recencyDays,
                'purchase_frequency_12m' => $purchaseFrequency12m,
                'last_90d_orders' => $last90dOrders,
                'last_90d_net_revenue' => $last90dNetRevenue,
                'products_purchased_count' => (int) ($distinctCounts->products_count ?? 0),
                'categories_purchased_count' => (int) ($distinctCounts->categories_count ?? 0),
                'return_rate_12m' => $returnRate12m,
                'email_opt_in' => $this->toBooleanString($preferences->marketing_emails ?? true),
                'sms_opt_in' => $this->toBooleanString($preferences->order_notifications ?? true),
                'loyalty_tier' => $loyaltyTier,
                'churn_risk_score' => $churnRiskScore,
            ];
        }
    }

    private function formatDateTime($value): ?string
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format('F j, Y g:iA');
    }

    private function toBooleanString($value): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOL) ? 'true' : 'false';
    }
}
