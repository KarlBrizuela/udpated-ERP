<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'date',
        'terms',
        'invoice_number',
        'total_amount',
        'status',
        'prepared_by',
        'approved_by',
        'currency',
        // Ford-specific fields
        'source',
        'vendor_name',
        'contact_persons',
        'vendor_address',
        'payment_schedule',
        'payment_schedule2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivingReports()
    {
        return $this->hasMany(ReceivingReport::class);
    }

    public function getCurrencySymbolAttribute()
    {
        if ($this->currency === 'PHP') {
            return '₱';
        }
        if ($this->currency === 'USD') {
            return '$';
        }
        return $this->source === 'ford' ? '$' : '₱';
    }

    public function getCurrencyLabelAttribute()
    {
        if ($this->currency === 'PHP') {
            return 'PHP';
        }
        if ($this->currency === 'USD') {
            return 'USD';
        }
        return $this->source === 'ford' ? 'USD' : 'PHP';
    }
}
