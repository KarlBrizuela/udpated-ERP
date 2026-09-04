<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'book_id',
        'type',
        'quantity',
        'location',
        'source',
        'supplier',
        'reference_number',
        'unit_cost',
        'total_cost',
        'notes',
        'status',
        'transaction_date',
        'user_id',
        'sales_order_item_id',
        'rider_collection_id',
        'related_transaction_id',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Inventory Transaction belongs to Sales Order Item (for evaluation tracking)
     */
    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class, 'sales_order_item_id');
    }

    /**
     * Relationship: Inventory Transaction belongs to Rider Collection (for evaluation returns/sales)
     */
    public function riderCollection(): BelongsTo
    {
        return $this->belongsTo(RiderCollection::class, 'rider_collection_id');
    }

    /**
     * Relationship: Related transaction (e.g., return pairs with original send)
     */
    public function relatedTransaction(): BelongsTo
    {
        return $this->belongsTo(InventoryTransaction::class, 'related_transaction_id');
    }

    /**
     * Get transaction type description
     * Types: in, out, out_evaluation, in_return_evaluation, out_sold_evaluation
     */
    public function getTypeLabel()
    {
        $labels = [
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'out_evaluation' => 'Sent for Evaluation',
            'in_return_evaluation' => 'Returned from Evaluation',
            'out_sold_evaluation' => 'Sold from Evaluation',
            'LOST' => 'Lost Inventory',
        ];

        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Check if this is an evaluation-related transaction
     */
    public function isEvaluationTransaction()
    {
        return in_array($this->type, ['out_evaluation', 'in_return_evaluation', 'out_sold_evaluation']);
    }
}
