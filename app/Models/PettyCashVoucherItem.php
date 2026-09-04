<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashVoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'petty_cash_voucher_id',
        'particulars',
        'amount',
        'expense_account_id',
    ];

    public function voucher()
    {
        return $this->belongsTo(PettyCashVoucher::class, 'petty_cash_voucher_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }
}
