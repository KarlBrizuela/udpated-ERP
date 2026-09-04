<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequest extends Model
{
    use HasFactory;

    protected $table = 'job_requests';

    protected $fillable = [
        'job_no',
        'project_title',
        'specifications',
        'due_date',
        'date',
        'department_id',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'dept_id');
    }
}
