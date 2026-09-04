<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\ReceivingReportItem;
use App\Models\User;

class ReceivingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'rr_number',
        'purchase_order_id',
        'supplier_id',
        'received_date',
        'received_by',
        'status',
        'notes',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(ReceivingReportItem::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getCurrencySymbolAttribute()
    {
        return $this->purchaseOrder ? $this->purchaseOrder->currency_symbol : '₱';
    }
}
