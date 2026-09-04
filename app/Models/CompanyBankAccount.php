<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_code',
        'bank_name',
        'account_name',
        'account_number',
        'account_type',
        'currency',
        'opening_balance',
        'current_balance',
        'status',
        'notes',
    ];

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class, 'bank_account_id')->latest('transaction_date');
    }

    public function recalculateBalance()
    {
        $opening = (float) $this->opening_balance;
        $inflows = (float) $this->transactions()->where('category', 'Inflow')->where('status', '!=', 'Cancelled')->sum('amount');
        $outflows = (float) $this->transactions()->where('category', 'Outflow')->where('status', '!=', 'Cancelled')->sum('amount');

        // Transfers out of this account
        $transfersOut = (float) $this->transactions()->where('transaction_type', 'Transfer')->where('status', '!=', 'Cancelled')->sum('amount');
        
        // Transfers into this account
        $transfersIn = (float) CashTransaction::where('to_bank_account_id', $this->id)->where('status', '!=', 'Cancelled')->sum('amount');

        $current = ($opening + $inflows + $transfersIn) - ($outflows + $transfersOut);
        $this->current_balance = round($current, 2);

        return $this;
    }
}
