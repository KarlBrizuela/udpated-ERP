<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_budget_id',
        'account_category',
        'allocated_amount',
        'actual_amount',
        'variance_amount',
        'notes',
    ];

    public function departmentBudget()
    {
        return $this->belongsTo(DepartmentBudget::class, 'department_budget_id');
    }

    public function recalculateVariance()
    {
        $allocated = (float) $this->allocated_amount;
        $actual = (float) $this->actual_amount;
        $this->variance_amount = round($allocated - $actual, 2);
        return $this;
    }
}
