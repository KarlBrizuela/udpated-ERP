<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $table = 'sales_invoices';
    
    protected $fillable = [
        'so_id',
        'so_number',
        'si_number',
        'customer_id',
        'customer_name',
        'transaction_type',
        'total_amount',
        'status',
        'created_by',
        'approved_by',
        'posted_by',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
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
     * Relationship to Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relationship to Invoice Items
     */
    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'si_id');
    }

    /**
     * Relationship to User (Created By)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User (Approved By)
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship to User (Posted By)
     */
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
