<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_request_id',
        'item_date',
        'ref_no',
        'particulars',
        'amount',
    ];

    protected $casts = [
        'item_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function paymentRequest()
    {
        return $this->belongsTo(PaymentRequest::class);
    }
}
