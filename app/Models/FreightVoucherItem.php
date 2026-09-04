<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightVoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'freight_voucher_id',
        'particulars',
        'amount',
        'expense_account_id',
    ];

    public function voucher()
    {
        return $this->belongsTo(FreightVoucher::class, 'freight_voucher_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }
}
