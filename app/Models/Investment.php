<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_code',
        'name',
        'type',
        'institution',
        'principal_amount',
        'current_value',
        'interest_rate',
        'acquisition_date',
        'maturity_date',
        'total_dividends',
        'total_interest',
        'total_return',
        'roi_percentage',
        'status',
        'notes',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'maturity_date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(InvestmentTransaction::class, 'investment_id')->latest('transaction_date');
    }

    public function recalculatePerformance()
    {
        $dividends = (float) $this->transactions()->where('transaction_type', 'Dividend')->sum('amount');
        $interest = (float) $this->transactions()->where('transaction_type', 'Interest')->sum('amount');

        $this->total_dividends = round($dividends, 2);
        $this->total_interest = round($interest, 2);

        $principal = max(1, (float) $this->principal_amount);
        $currentVal = (float) $this->current_value;

        $totalReturn = ($currentVal + $dividends + $interest) - $principal;
        $roi = ($totalReturn / $principal) * 100;

        $this->total_return = round($totalReturn, 2);
        $this->roi_percentage = round($roi, 2);

        return $this;
    }
}
