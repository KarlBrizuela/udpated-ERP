<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'transaction_type',
        'transaction_date',
        'amount',
        'reference_no',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }
}
