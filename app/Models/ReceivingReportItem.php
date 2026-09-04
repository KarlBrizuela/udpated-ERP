<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingReportItem extends Model
{
    protected $fillable = [
        'receiving_report_id',
        'purchase_order_item_id',
        'product_id',
        'quantity_received',
        'unit_cost',
        'total_cost',
    ];

    public function receivingReport()
    {
        return $this->belongsTo(ReceivingReport::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
