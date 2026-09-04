<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FreightQuotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_number',
        'quote_date',
        'validity_days',
        'customer_id',
        'customer_representative',
        'transaction_type',
        'origin_contact',
        'origin_address',
        'origin_province',
        'destination_contact',
        'destination_address',
        'destination_province',
        'service_mode',
        'freight_mode',
        'forwarder',
        'freight_option',
        'currency',
        'service_carrier',
        'service_remarks',
        'cargo_items',
        'estimated_freight',
        'valuation_percentage',
        'valuation_charge',
        'handling_percentage',
        'handling_fee',
        'total_amount',
        'status',
        'workflow_status',
        'source',
        'sales_order_id',
        'boxes_count',
        'logistics_notes',
        'created_by',
        'approved_by',
        'approved_at',
        'responded_by',
        'responded_at',
        'notes',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'approved_at' => 'datetime',
        'responded_at' => 'datetime',
        'cargo_items' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
