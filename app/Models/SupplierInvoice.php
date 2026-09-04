<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'purchase_order_id',
        'receiving_report_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'withholding_tax_rate',
        'withholding_tax_amount',
        'total_amount',
        'amount_paid',
        'status',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receivingReport()
    {
        return $this->belongsTo(ReceivingReport::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function getBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->amount_paid);
    }
}
