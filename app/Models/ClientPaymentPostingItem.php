<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPaymentPostingItem extends Model
{
    protected $fillable = [
        'posting_id',
        'customer_id',
        'invoice_no',
        'receipt_no',
        'reference_no',
        'payment_method',
        'chart_of_account_id',
        'check_number',
        'check_date',
        'bank_name',
        'payment_date',
        'bank_date',
        'document_no',
        'amount',
        'proof_attachment',
    ];

    public function posting()
    {
        return $this->belongsTo(ClientPaymentPosting::class, 'posting_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
