<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sales_order_id',
        'rider_collection_id', // Link to COD collection
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'reference_number',
        'collected_by', // Rider ID who collected
        'handed_over_by', // Cashier ID who received
        'verified_by', // Accounting who verified
        'notes',
        'proof_of_payment'
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function riderCollection()
    {
        return $this->belongsTo(RiderCollection::class, 'rider_collection_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function handedOverBy()
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

