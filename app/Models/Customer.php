<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'company_name',
        'account_number',
        'opening_balance',
        'opening_balance_date',
        'currency_code',
        'customer_type',
        'rep',
        'class',
        'title',
        'first_name',
        'middle_initial',
        'last_name',
        'job_title',
        'main_phone',
        'home_phone',
        'work_phone',
        'mobile',
        'fax',
        'main_email',
        'cc_email',
        'website',
        'other_contact',
        'billing_address',
        'billing_address_line1',
        'billing_address_line2',
        'billing_city',
        'billing_province',
        'billing_country',
        'shipping_address',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_province',
        'shipping_country',
        'is_default_shipping',
        'payment_terms',
        'preferred_delivery_method',
        'preferred_payment_method',
        'credit_limit',
        'price_level',
        'card_number_last4',
        'card_exp_month',
        'card_exp_year',
        'card_name',
        'card_billing_address',
        'card_zip',
        'custom_contact_person',
        'custom_customer_field',
        'representatives',
        'is_inactive',
        'manual_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
        'is_default_shipping' => 'boolean',
        'credit_limit' => 'decimal:2',
        'is_inactive' => 'boolean',
        'representatives' => 'array',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'customer_id';
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'customer_id', 'customer_id');
    }

    public function getBalanceAttribute()
    {
        $openingBalance = (float)($this->opening_balance ?? 0);
        $activeOrders = $this->salesOrders()
            ->where('payment_status', '!=', 'paid')
            ->where(function($q) {
                $q->whereNull('proof_of_payment')->orWhere('proof_of_payment', '');
            })
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->where('status', '!=', 'cancelled')
            ->get();
        
        $unpaidBalance = $activeOrders->sum(function($order) {
            return $order->remaining_balance;
        });

        return $openingBalance + $unpaidBalance;
    }

    public function getIsBadClientAttribute()
    {
        if ($this->manual_status === 'bad') {
            return true;
        }
        if ($this->manual_status === 'good') {
            return false;
        }

        // System logic
        $balance = $this->balance;
        if ($balance <= 0) {
            return false;
        }

        // Rule 1: Balance over 10,000
        if ($balance >= 10000) {
            return true;
        }

        // Rule 2: Unpaid orders older than 3 months
        $threeMonthsAgo = now()->subMonths(3);
        $hasOldUnpaid = $this->salesOrders()
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '<', $threeMonthsAgo)
            ->exists();

        if ($hasOldUnpaid) {
            return true;
        }

        // Rule 3: Any overdue order (past its due date)
        $unpaidOrders = $this->salesOrders()
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($unpaidOrders as $order) {
            if ($order->due_date && $order->due_date->isPast()) {
                return true;
            }
        }
        
        return false;
    }
}
