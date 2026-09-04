<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_code',
        'fiscal_year',
        'division',
        'department',
        'allocated_budget',
        'actual_spend',
        'variance',
        'percentage_used',
        'forecasted_spend',
        'status',
        'notes',
    ];

    public function lineItems()
    {
        return $this->hasMany(BudgetLineItem::class, 'department_budget_id');
    }

    public function recalculateMetrics()
    {
        $allocated = max(1, (float) $this->allocated_budget);
        
        if ($this->lineItems()->count() > 0) {
            $actual = (float) $this->lineItems()->sum('actual_amount');
            $this->actual_spend = round($actual, 2);
        } else {
            $actual = (float) $this->actual_spend;
        }

        $variance = $allocated - $actual;
        $pctUsed = ($actual / $allocated) * 100;

        // Forecast based on elapsed months in fiscal year (assuming current month)
        $currentMonth = max(1, (int) date('n'));
        $forecast = ($actual / $currentMonth) * 12;

        $this->variance = round($variance, 2);
        $this->percentage_used = round($pctUsed, 2);
        $this->forecasted_spend = round($forecast, 2);

        return $this;
    }
}
