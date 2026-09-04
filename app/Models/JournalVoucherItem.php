<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'jv_request_id',
        'reference_no',
        'customer_name',
        'customer_id',
        'amount',
        'remarks',
        'type'
    ];

    public function request()
    {
        return $this->belongsTo(JournalVoucherRequest::class, 'jv_request_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
