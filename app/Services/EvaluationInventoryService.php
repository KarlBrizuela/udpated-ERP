<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\InventoryTransaction;
use App\Models\RiderCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for auditing and reconciling evaluation order inventory
 */
class EvaluationInventoryService
{
    /**
     * Get evaluation order reconciliation report
     * Shows: sent items vs returned items vs sold items
     */
    public static function getEvaluationReconciliation(SalesOrder $salesOrder)
    {
        if ($salesOrder->type !== 'evaluation') {
            throw new \InvalidArgumentException('This is not an evaluation order');
        }

        $reconciliation = [
            'sales_order' => $salesOrder->so_number,
            'customer' => $salesOrder->customer->customer_name ?? 'Unknown',
            'items' => [],
            'totals' => [
                'total_sent' => 0,
                'total_sold' => 0,
                'total_returned' => 0,
                'balance' => 0,
            ],
            'status' => 'pending', // pending, reconciled, discrepancy
        ];

        // Get all items from the evaluation order
        foreach ($salesOrder->items as $item) {
            $itemData = [
                'book_id' => $item->book_id,
                'book_name' => $item->book->name ?? 'Unknown',
                'sent_qty' => $item->sent_qty ?? $item->quantity,
                'sold_qty' => $item->selected_qty ?? $item->customer_selected_qty ?? 0,
                'returned_qty' => $item->returned_qty ?? 0,
                'unit_price' => $item->price,
            ];

            // Get transaction details
            $transactions = InventoryTransaction::where('sales_order_item_id', $item->id)
                ->whereIn('type', ['out_evaluation', 'in_return_evaluation', 'out_sold_evaluation'])
                ->get();

            $itemData['transactions'] = $transactions->map(function ($txn) {
                return [
                    'id' => $txn->id,
                    'type' => $txn->getTypeLabel(),
                    'quantity' => $txn->quantity,
                    'date' => $txn->transaction_date?->format('M d, Y H:i'),
                ];
            })->toArray();

            $reconciliation['items'][] = $itemData;

            // Add to totals
            $reconciliation['totals']['total_sent'] += $itemData['sent_qty'];
            $reconciliation['totals']['total_sold'] += $itemData['sold_qty'];
            $reconciliation['totals']['total_returned'] += $itemData['returned_qty'];
        }

        // Calculate balance
        $reconciliation['totals']['balance'] = 
            $reconciliation['totals']['total_sent'] - 
            ($reconciliation['totals']['total_sold'] + $reconciliation['totals']['total_returned']);

        // Determine status
        if ($reconciliation['totals']['balance'] === 0) {
            $reconciliation['status'] = 'reconciled';
        } elseif ($reconciliation['totals']['balance'] !== 0) {
            $reconciliation['status'] = 'discrepancy';
        }

        return $reconciliation;
    }

    /**
     * Get all evaluation orders with pending reconciliation
     */
    public static function getPendingEvaluations()
    {
        return SalesOrder::where('type', 'evaluation')
            ->with(['items', 'riderCollection', 'customer'])
            ->whereDoesntHave('riderCollection', function ($query) {
                $query->whereNotNull('evaluation_completed_at');
            })
            ->orWhere(function ($query) {
                $query->where('type', 'evaluation')
                    ->has('riderCollection')
                    ->whereHas('riderCollection', function ($q) {
                        $q->whereNotNull('evaluation_completed_at');
                    });
            })
            ->get();
    }

    /**
     * Get inventory transaction audit trail for evaluation order
     */
    public static function getEvaluationTransactionAudit(SalesOrder $salesOrder)
    {
        $transactions = InventoryTransaction::whereHas('salesOrderItem', function ($query) use ($salesOrder) {
            $query->where('sales_order_id', $salesOrder->id);
        })
        ->orWhere(function ($query) use ($salesOrder) {
            $query->whereHas('riderCollection', function ($q) use ($salesOrder) {
                $q->where('sales_order_id', $salesOrder->id);
            });
        })
        ->with('book', 'user', 'salesOrderItem', 'riderCollection')
        ->orderBy('created_at', 'asc')
        ->get();

        return $transactions->map(function ($txn) {
            return [
                'date' => $txn->created_at->format('M d, Y H:i:s'),
                'type' => $txn->getTypeLabel(),
                'book' => $txn->book->name ?? 'Unknown',
                'quantity' => $txn->quantity,
                'status' => $txn->status,
                'notes' => $txn->notes,
                'recorded_by' => $txn->user->name ?? 'System',
            ];
        })->toArray();
    }

    /**
     * Get evaluation summary report for analytics
     * Returns stats on evaluation orders vs COD orders
     */
    public static function getEvaluationSummaryReport($dateFrom = null, $dateTo = null)
    {
        $query = SalesOrder::where('type', 'evaluation');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $evaluations = $query->with(['items', 'riderCollection', 'customer'])->get();

        $report = [
            'period' => [
                'from' => $dateFrom?->format('M d, Y') ?? 'All time',
                'to' => $dateTo?->format('M d, Y') ?? 'Now',
            ],
            'total_evaluations' => $evaluations->count(),
            'completed' => $evaluations->filter(fn ($e) => $e->riderCollection?->evaluation_completed_at)->count(),
            'pending' => $evaluations->filter(fn ($e) => !$e->riderCollection?->evaluation_completed_at)->count(),
            'stats' => [
                'total_items_sent' => 0,
                'total_items_sold' => 0,
                'total_items_returned' => 0,
                'avg_conversion_rate' => 0,
                'total_sales_value' => 0,
            ],
        ];

        $totalSent = 0;
        $totalSold = 0;

        foreach ($evaluations as $eo) {
            $sent = $eo->items->sum('sent_qty') ?? $eo->items->sum('quantity');
            $sold = $eo->items->sum('selected_qty') ?? $eo->items->sum('customer_selected_qty') ?? 0;

            $totalSent += $sent;
            $totalSold += $sold;
            $report['stats']['total_items_returned'] += ($sent - $sold);

            // Calculate sales value (only sold items)
            $soValue = $eo->items->sum(function ($item) {
                return ($item->selected_qty ?? $item->customer_selected_qty ?? 0) * $item->price;
            });
            $report['stats']['total_sales_value'] += $soValue;
        }

        $report['stats']['total_items_sent'] = $totalSent;
        $report['stats']['total_items_sold'] = $totalSold;
        $report['stats']['avg_conversion_rate'] = $totalSent > 0 
            ? round(($totalSold / $totalSent) * 100, 2) 
            : 0;

        return $report;
    }
}
