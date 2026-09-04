<?php

namespace App\Models\Admin\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CCTVReq extends Model
{
    use HasFactory;

    protected $table = 'mis_cctv_requests';
    protected $primaryKey = 'cctv_req_id';

    protected $fillable = [
        'user_id',
        'department',
        'status',
        'requested_by',
        'date_of_incident',
        'time_of_incident',
        'purpose',
        'hardcopy',
        'viewing',
        'attachment',
        'approved_by_manager',
        'manager_approved_at',
        'approved_by_hr',
        'hr_approved_at',
        'approved_by_director',
        'director_approved_at',
        'completed_by',
        'completed_at',
        'rejected_by',
        'rejected_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_manager');
    }

    public function hr()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_hr');
    }

    public function director()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_director');
    }

    public function rejector()
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }

    public function creatorDivisionKey()
    {
        $creator = $this->user;
        $division = $creator->division ?? '';

        if (str_contains($division, 'Marketing')) {
            return 'marketing';
        }

        if (str_contains($division, 'Production')) {
            return 'production';
        }

        if (str_contains($division, 'Admin') || str_contains($division, 'Finance')) {
            return 'admin-finance';
        }

        if ($creator) {
            foreach ($creator->divisions as $creatorDivision) {
                $division = $creatorDivision->division ?? '';

                if (str_contains($division, 'Marketing')) {
                    return 'marketing';
                }

                if (str_contains($division, 'Production')) {
                    return 'production';
                }

                if (str_contains($division, 'Admin') || str_contains($division, 'Finance')) {
                    return 'admin-finance';
                }
            }
        }

        return null;
    }

    private function userBelongsToDivision($user, $division)
    {
        if (str_contains($user->division ?? '', $division)) {
            return true;
        }

        return $user->divisions()->where('division', 'like', "%{$division}%")->exists();
    }

    private function isDivisionApprover($user, $division)
    {
        if ($user->position === 'Super Admin') {
            return true;
        }

        $isApproverPosition = str_contains($user->position ?? '', 'Manager')
            || str_contains($user->position ?? '', 'Supervisor');

        return $isApproverPosition && $this->userBelongsToDivision($user, $division);
    }

    private function isAdminFinanceManager($user)
    {
        if ($user->position === 'Super Admin') {
            return true;
        }

        return str_contains($user->position ?? '', 'Manager')
            && $this->userBelongsToDivision($user, 'Admin');
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
            case 'to submit':
                return $this->user_id === $user->id;
            case 'pending approval':
                $creatorDivision = $this->creatorDivisionKey();

                if ($creatorDivision === 'marketing') {
                    return $this->isDivisionApprover($user, 'Marketing');
                }

                if ($creatorDivision === 'production') {
                    return $this->isDivisionApprover($user, 'Production');
                }

                if ($creatorDivision === 'admin-finance') {
                    return $this->isAdminFinanceManager($user) || $user->position === 'MIS Supervisor';
                }

                return false;
            case 'Pending HR approval':
                return in_array($user->position, ['HR Manager', 'HR Specialist', 'HR Staff']);
            case 'Pending Final Approval':
                return $this->isAdminFinanceManager($user);
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
            'to submit' => 'pending approval',
            'pending approval' => 'Pending HR approval',
            'Pending HR approval' => 'Pending Final Approval',
            'Pending Final Approval' => 'on_hold',
        ];

        return $flow[$this->status] ?? null;
    }

    /**
     * Get the database field name for tracking the approver at the current stage.
     */
    public function getApproverFieldForCurrentStatus()
    {
        $mapping = [
            'pending approval' => 'approved_by_manager',
            'Pending HR approval' => 'approved_by_hr',
            'Pending Final Approval' => 'approved_by_director',
        ];

        return $mapping[$this->status] ?? null;
    }

    /**
     * Get the timestamp field name for the current stage.
     */
    public function getTimestampFieldForCurrentStatus()
    {
        $mapping = [
            'pending approval' => 'manager_approved_at',
            'Pending HR approval' => 'hr_approved_at',
            'Pending Final Approval' => 'director_approved_at',
        ];

        return $mapping[$this->status] ?? null;
    }
}
