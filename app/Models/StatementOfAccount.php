<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'soa_number',
        'customer_id',
        'contact_person',
        'billing_address',
        'billing_period_start',
        'billing_period_end',
        'total_amount',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(StatementOfAccountItem::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }
}
