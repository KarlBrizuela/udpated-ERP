<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCashAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_name',
        'employee_number',
        'department',
        'department_source',
        'position',
        'amount',
        'purpose',
        'date_needed',
        'disbursement_method',
        'disbursement_reference',
        'disbursement_date',
        'gl_account_code',
        'status',
        'rejection_reason',
        'approved_by_manager',
        'manager_approved_at',
        'approved_by_admin',
        'admin_approved_at',
        'approved_by_director',
        'director_approved_at',
        'rejected_by',
        'rejected_at'
    ];

    protected $casts = [
        'date_needed' => 'date',
        'disbursement_date' => 'date',
        'manager_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'director_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'approved_by_admin');
    }

    public function director()
    {
        return $this->belongsTo(User::class, 'approved_by_director');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if the user can approve the request based on its current status.
     */
    public function canBeApprovedBy($user)
    {
        if ($user->position === 'Super Admin') {
            return true;
        }

        switch ($this->status) {
            case 'pending_supervisor_approval':
                // Production Division Specific Logic
                if ($this->department_source === 'Production') {
                    $productionApprovers = ['DTO Supervisor', 'Senior Ford Staff', 'Senior Logistics Staff'];
                    if (in_array($user->position, $productionApprovers)) {
                        return true;
                    }
                    if (str_contains($user->position, 'Manager') && str_contains($user->division, 'Production')) {
                        return true;
                    }
                    return false;
                }

                // Standard Logic for other divisions
                return str_contains($user->position, 'Manager'); 
            case 'pending_admin_approval':
                // For the second stage, any Manager in Admin or Finance can approve
                $isAFManager = str_contains($user->position, 'Manager') && 
                              (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance'));
                return $isAFManager || in_array($user->position, ['Admin & Finance Manager', 'Finance Manager']);
            case 'pending_director_approval':
                return $user->position === 'Director';
            default:
                return false;
        }
    }

    /**
     * Get the next status in the approval workflow.
     */
    public function getNextApprovalStatus()
    {
        $flow = [
            'pending_supervisor_approval' => 'pending_admin_approval',
            'pending_admin_approval' => 'pending_director_approval',
            'pending_director_approval' => 'approved',
        ];

        return $flow[$this->status] ?? null;
    }

    /**
     * Get the database field name for tracking the approver at the current stage.
     */
    public function getApproverFieldForCurrentStatus()
    {
        $mapping = [
            'pending_supervisor_approval' => 'approved_by_manager',
            'pending_admin_approval' => 'approved_by_admin',
            'pending_director_approval' => 'approved_by_director',
        ];

        return $mapping[$this->status] ?? null;
    }

    /**
     * Get the timestamp field name for the current stage.
     */
    public function getTimestampFieldForCurrentStatus()
    {
        $mapping = [
            'pending_supervisor_approval' => 'manager_approved_at',
            'pending_admin_approval' => 'admin_approved_at',
            'pending_director_approval' => 'director_approved_at',
        ];

        return $mapping[$this->status] ?? null;
    }
}
