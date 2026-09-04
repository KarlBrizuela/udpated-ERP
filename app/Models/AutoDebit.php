<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoDebit extends Model
{
    protected $fillable = [
        'date',
        'amount',
        'debit_date',
        'item_reason',
        'source_origin',
        'prepared_by',
        'director_approved_by',
        'director_approved_at',
        'finance_approved_by',
        'finance_approved_at',
        'status',
    ];

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function directorApprover()
    {
        return $this->belongsTo(User::class, 'director_approved_by');
    }

    public function financeApprover()
    {
        return $this->belongsTo(User::class, 'finance_approved_by');
    }
}
