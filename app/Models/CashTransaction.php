<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_no',
        'bank_account_id',
        'to_bank_account_id',
        'transaction_type',
        'category',
        'amount',
        'reference_no',
        'payee_or_payer',
        'transaction_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(CompanyBankAccount::class, 'bank_account_id');
    }

    public function destinationBankAccount()
    {
        return $this->belongsTo(CompanyBankAccount::class, 'to_bank_account_id');
    }
}
