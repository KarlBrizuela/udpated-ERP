<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'customer_id',
        'carrier',
        'amount',
        'status',
        'bill_date',
        'notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
