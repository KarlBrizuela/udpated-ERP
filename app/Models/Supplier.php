<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'company_name',
        'category',
        'contact_person',
        'email',
        'phone',
        'tin',
        'address',
        'tax_rate',
        'terms',
        'status',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function receivingReports()
    {
        return $this->hasMany(ReceivingReport::class);
    }

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
