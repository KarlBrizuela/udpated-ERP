<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'title',
        'amount',
        'expense_date',
        'department_id',
        'added_by',
        'notes',
    ];

    /**
     * Get the department associated with the expense.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'dept_id');
    }

    /**
     * Get the user who added the expense.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
