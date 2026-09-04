<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_representative',
        'customer_contact',
        'area_sales_staff_id',
        'so_number',
        'si_number',
        'type',
        'currency',
        'transaction_type', // COD, Credit, Prepaid, Check, Other
        'terms',
        'ref_number',
        'status', // draft, pending_mkt_approval, pending_acct_approval, picking, gathered, pending_si_prep, pending_si_approval, pending_dr_prep, pending_dr_approval, ready_for_delivery, completed, cancelled
        'stock_deducted',
        'total_amount',
        'tax_amount',
        'withholding_tax_amount',
        'discount_amount',
        'discount_percentage',
        'is_non_vat',
        'prepared_by',
        'approved_by_mkt',
        'approved_by_acct',
        'approved_by_prod',
        'signed_by_af_manager',
        'si_prepared_by',
        'dr_prepared_by',
        'dr_approved_by',
        'mkt_approved_at',
        'acct_approved_at',
        'prod_approved_at',
        'signed_at',
        'si_prepared_at',
        'dr_prepared_at',
        'dr_approved_at',
        'ar_prepared_at',
        'ar_prepared_by',
        'cr_prepared_at',
        'cr_prepared_by',
        'remarks',
        'cancellation_date',
        'billing_address',
        'payment_method',
        'payment_reference',
        'cash_received',
        'change_amount',
        'platform',
        'payment_status',
        'collection_status', // pending_collection, collected, handed_over, reconciled
        'tracking_number',
        'shipping_address',
        'attachment',
        'proof_of_payment',
        'order_list_attachment',
        'transaction_subtype',
        'ecom_platform',
        'platform_order_id',
        'pick_list_attachment',
        'shipping_label_attachment',
        'driver',
        'plate_number',
        'helper',
        'is_pickup',
        'picked_up_at',
        'driver_id',
        'delivery_date',
        'packing_data',
        'packing_prepared_by',
        'picked_at',
        'picked_by',
        'freight_charges',
        'freight_notes',
        'freight_option',
        'forwarder',
        'ecom_payout_status',
        'driver_approval_status',
        'driver_approved_by',
        'driver_approved_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function areaSalesStaff()
    {
        return $this->belongsTo(User::class, 'area_sales_staff_id');
    }

    public function driverApprovedBy()
    {
        return $this->belongsTo(User::class, 'driver_approved_by');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function mktApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_mkt');
    }

    public function acctApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_acct');
    }

    public function prodApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_prod');
    }

    public function siPreparedBy()
    {
        return $this->belongsTo(User::class, 'si_prepared_by');
    }

    public function drPreparedBy()
    {
        return $this->belongsTo(User::class, 'dr_prepared_by');
    }

    public function drApprovedBy()
    {
        return $this->belongsTo(User::class, 'dr_approved_by');
    }

    public function arPreparedBy()
    {
        return $this->belongsTo(User::class, 'ar_prepared_by');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by_af_manager');
    }

    public function driverUser()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function riderCollection()
    {
        return $this->hasOne(RiderCollection::class, 'sales_order_id');
    }

    public function pickLists()
    {
        return $this->hasMany(PickList::class, 'sales_order_id');
    }

    public function freightQuotation()
    {
        return $this->hasOne(FreightQuotation::class, 'sales_order_id');
    }

    public function invoice()
    {
        return $this->hasOne(SalesInvoice::class, 'so_id');
    }

    public function deliveryReceipt()
    {
        return $this->hasOne(DeliveryReceipt::class, 'so_id');
    }

    public function invoices()
    {
        return $this->hasMany(SalesInvoice::class, 'so_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'sales_order_id');
    }

    /**
     * Get total amount paid for this order
     */
    public function getTotalPaidAmountAttribute()
    {
        $paidFromDb = (float) $this->payments()->sum('amount');

        if ($paidFromDb > 0) {
            return $paidFromDb;
        }

        $isPosOrEcom = in_array($this->type, ['calculator_pos', 'ecom_direct']);
        if ($this->payment_status === 'paid' || $isPosOrEcom) {
            return (float) $this->total_amount;
        }

        return 0.0;
    }

    /**
     * Get remaining balance for this order
     */
    public function getRemainingBalanceAttribute()
    {
        if ($this->type === 'complimentary') {
            return 0.0;
        }
        $total = (float)($this->total_amount && (float)$this->total_amount > 0 ? $this->total_amount : $this->final_total);
        return max(0, $total - $this->total_paid_amount);
    }

    /**
     * Get computed payment status ('paid', 'partially_paid', 'unpaid', 'complimentary')
     */
    public function getComputedPaymentStatusAttribute()
    {
        if ($this->type === 'complimentary') {
            return 'complimentary';
        }
        $paid = $this->total_paid_amount;
        $total = (float)$this->total_amount;

        if ($paid >= $total && $total > 0) {
            return 'paid';
        }
        if ($paid > 0 && $paid < $total) {
            return 'partially_paid';
        }
        return $this->payment_status === 'partially_paid' ? 'partially_paid' : 'unpaid';
    }

    /**
     * Get all activity logs related to this sales order
     */
    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'reference_id')
            ->where('reference_type', 'SalesOrder');
    }

    public function getDueDateAttribute()
    {
        $baseDate = $this->si_prepared_at ?: ($this->created_at ?: now());
        $days = 0;
        
        $termsRaw = strtolower(trim($this->terms ?? ''));
        if (preg_match('/(\d+)/', $termsRaw, $matches)) {
            $days = (int) $matches[1];
        } else {
            switch ($termsRaw) {
                case 'net 15': case '15_days': case '15 days': $days = 15; break;
                case 'net 30': case '30_days': case '30 days': $days = 30; break;
                case 'net 60': case '60_days': case '60 days': $days = 60; break;
                case 'net 90': case '90_days': case '90 days': $days = 90; break;
                default: $days = 0;
            }
        }
        
        return \Carbon\Carbon::parse($baseDate)->addDays($days);
    }

    public function getFinalTotalAttribute()
    {
        if ($this->total_amount !== null && (float) $this->total_amount > 0) {
            return (float) $this->total_amount;
        }

        $itemsSubtotal = $this->relationLoaded('items')
            ? $this->items->sum('subtotal')
            : $this->items()->sum('subtotal');

        $freightCharges = (float) ($this->freight_charges ?? 0);
        $serviceFee = $this->freight_option === 'freight_collect' ? 50.00 : 0;
        $discount = (float) ($this->discount_amount ?? 0);

        return max(0, $itemsSubtotal + $freightCharges + $serviceFee - $discount);
    }
}
