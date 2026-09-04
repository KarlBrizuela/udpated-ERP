<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'supplier_id',
        'supplier_invoice_id',
        'payment_date',
        'amount_paid',
        'withholding_tax_amount',
        'payment_method',
        'reference_number',
        'status',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }
}
