<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceiptItem extends Model
{
    use HasFactory;

    protected $table = 'delivery_receipt_items';

    protected $fillable = [
        'dr_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'amount'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    /**
     * Relationship to Delivery Receipt
     */
    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class, 'dr_id');
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
