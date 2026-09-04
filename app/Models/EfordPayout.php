<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfordPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'prepared_by',
        'period',
        'customer_id',
        'total_amount',
        'total_freight',
        'total_gross_sales',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'total_amount' => 'decimal:2',
        'total_freight' => 'decimal:2',
        'total_gross_sales' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(EfordPayoutItem::class, 'eford_payout_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
