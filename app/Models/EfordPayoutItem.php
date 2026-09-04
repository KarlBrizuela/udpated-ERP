<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfordPayoutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'eford_payout_id',
        'order_no',
        'date',
        'si_no',
        'customer_name',
        'amount',
        'freight',
        'gross_sales',
        'payment_method',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'freight' => 'decimal:2',
        'gross_sales' => 'decimal:2',
    ];

    public function payout()
    {
        return $this->belongsTo(EfordPayout::class, 'eford_payout_id');
    }
}
