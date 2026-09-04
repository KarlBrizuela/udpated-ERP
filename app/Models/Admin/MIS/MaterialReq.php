<?php

namespace App\Models\Admin\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReq extends Model
{
    use HasFactory;

    protected $table = 'mis_material_reqs';
    protected $primaryKey = 'material_req_id';

    protected $fillable = [
        'user_id',
        'module',
        'requested_by',
        'request_date',
        'request_details',
        'amount',
        'status',
        'approved_by_manager',
        'manager_approved_at',
        'approved_by_admin',
        'admin_approved_at',
        'approved_by_director',
        'director_approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_manager');
    }

    public function adminApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_admin');
    }

    public function director()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_director');
    }

    public function rejector()
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }

    /**
     * Check if the user can approve the request based on its current status.
     */
    public function canBeApprovedBy($user)
    {
        if ($user->position === 'Super Admin') {
            return true;
        }

        if ($this->module === 'Direct') {
            switch ($this->status) {
                case 'pending_supervisor_approval':
                    $isApproverManagerOrSupervisor = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor');
                    if (!$isApproverManagerOrSupervisor) {
                        return false;
                    }
                    
                    $requestor = $this->user;
                    if (!$requestor) {
                        return false;
                    }

                    $divisionMap = [
                        'Admin & Finance Division' => 'Admin',
                        'Marketing Division' => 'Marketing',
                        'Production Division' => 'Production',
                        'MIS Division' => 'MIS',
                        'GSD Division' => 'GSD',
                    ];
                    
                    $requestorDiv = $divisionMap[$requestor->division] ?? $requestor->division;
                    $approverDiv = $divisionMap[$user->division] ?? $user->division;

                    if (strcasecmp($requestorDiv, $approverDiv) === 0 || 
                        str_contains(strtolower($user->division), strtolower($requestorDiv)) || 
                        str_contains(strtolower($requestor->division), strtolower($approverDiv))) {
                        return true;
                    }

                    if ($requestor->department && $user->department && 
                        strcasecmp($requestor->department, $user->department) === 0) {
                        return true;
                    }

                    return false;

                case 'pending_admin_approval':
                    $isAFManager = str_contains($user->position, 'Manager') && 
                                  (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance'));
                    return $isAFManager || in_array($user->position, ['Admin & Finance Manager', 'Finance Manager']);

                case 'pending_director_approval':
                    return $user->position === 'Director';

                default:
                    return false;
            }
        }

        switch ($this->status) {
            case 'to submit':
                return $this->user_id === $user->id;
            case 'pending approval':
                return in_array($user->position, ['Manager', 'MIS Supervisor']);
            case 'Pending Final Approval':
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
        if ($this->module === 'Direct') {
            $flow = [
                'pending_supervisor_approval' => 'pending_admin_approval',
                'pending_admin_approval' => 'pending_director_approval',
                'pending_director_approval' => 'forwarded to accounting',
            ];
            return $flow[$this->status] ?? null;
        }

        $flow = [
            'to submit' => 'pending approval',
            'pending approval' => 'Pending Final Approval',
            'Pending Final Approval' => 'forwarded to accounting',
            'forwarded to accounting' => 'received',
        ];

        return $flow[$this->status] ?? null;
    }

    /**
     * Get the database field name for tracking the approver at the current stage.
     */
    public function getApproverFieldForCurrentStatus()
    {
        if ($this->module === 'Direct') {
            $mapping = [
                'pending_supervisor_approval' => 'approved_by_manager',
                'pending_admin_approval' => 'approved_by_admin',
                'pending_director_approval' => 'approved_by_director',
            ];
            return $mapping[$this->status] ?? null;
        }

        $mapping = [
            'pending approval' => 'approved_by_manager',
            'Pending Final Approval' => 'approved_by_director',
        ];

        return $mapping[$this->status] ?? null;
    }

    /**
     * Get the timestamp field name for the current stage.
     */
    public function getTimestampFieldForCurrentStatus()
    {
        if ($this->module === 'Direct') {
            $mapping = [
                'pending_supervisor_approval' => 'manager_approved_at',
                'pending_admin_approval' => 'admin_approved_at',
                'pending_director_approval' => 'director_approved_at',
            ];
            return $mapping[$this->status] ?? null;
        }

        $mapping = [
            'pending approval' => 'manager_approved_at',
            'Pending Final Approval' => 'director_approved_at',
        ];

        return $mapping[$this->status] ?? null;
    }
}
