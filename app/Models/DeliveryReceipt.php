<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;

    protected $table = 'delivery_receipts';

    protected $fillable = [
        'dr_number',
        'so_id',
        'so_number',
        'si_id',
        'si_number',
        'customer_id',
        'customer_name',
        'delivery_address',
        'total_amount',
        'delivery_date',
        'status',
        'prepared_by',
        'received_by',
        'prepared_at',
        'received_at',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_date' => 'date',
        'prepared_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship to Sales Order
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'so_id');
    }

    /**
     * Relationship to Sales Invoice
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'si_id');
    }

    /**
     * Relationship to Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Relationship to Delivery Receipt Items
     */
    public function items()
    {
        return $this->hasMany(DeliveryReceiptItem::class, 'dr_id');
    }

    /**
     * Relationship to User (Prepared By)
     */
    public function preparedByUser()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * Relationship to User (Received By)
     */
    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Calculate remaining date based on SO terms
     */
    public function getRemainingDateAttribute()
    {
        if (!$this->salesOrder || !$this->delivery_date) {
            return null;
        }

        $daysFromTerms = $this->getDaysFromTerms($this->salesOrder->terms);
        if ($daysFromTerms === 0) {
            return $this->delivery_date;
        }

        return now()->addDays($daysFromTerms)->format('Y-m-d');
    }

    /**
     * Get days from terms string
     */
    private function getDaysFromTerms($terms)
    {
        if (!$terms) {
            return 0;
        }

        $termsMap = [
            'cash' => 0,
            'cod' => 0,
            '7_days' => 7,
            '15_days' => 15,
            '30_days' => 30,
            '60_days' => 60,
            '90_days' => 90,
        ];

        return $termsMap[$terms] ?? 0;
    }
}
