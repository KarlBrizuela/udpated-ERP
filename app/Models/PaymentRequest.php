<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'date',
        'payment_to',
        'payment_for',
        'due_date',
        'po_number',
        'item_receipt',
        'total_amount',
        'attachment_path',
        'status',
        'director_approved_by',
        'director_approved_at',
        'admin_approved_by',
        'admin_approved_at',
        'finance_approved_by',
        'finance_approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'scheduled_payment_date',
        'payment_method',
        'payment_reference',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'scheduled_payment_date' => 'date',
        'total_amount' => 'decimal:2',
        'director_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'finance_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PaymentRequestItem::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function directorApprovedBy()
    {
        return $this->belongsTo(User::class, 'director_approved_by');
    }

    public function adminApprovedBy()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    public function financeApprovedBy()
    {
        return $this->belongsTo(User::class, 'finance_approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if the user can approve this request at its current stage.
     */
    public function canBeApprovedBy($user)
    {
        if ($this->status === 'rejected') {
            return false;
        }

        if ($user->position === 'Super Admin') {
            return true;
        }

        if ($this->status === 'pending_director_approval') {
            return $user->position === 'Director' || str_contains(strtolower($user->position ?? ''), 'director') || (!empty($user->role) && str_contains(strtolower($user->role), 'director'));
        }

        if ($this->status === 'pending_admin_finance_approval') {
            return $this->canApproveAsAdmin($user) || $this->canApproveAsFinance($user);
        }

        return false;
    }

    /**
     * Check if user is eligible to approve as Admin Manager.
     */
    public function canApproveAsAdmin($user)
    {
        if ($this->status !== 'pending_admin_finance_approval' || !is_null($this->admin_approved_by)) {
            return false;
        }
        $isAFManager = str_contains($user->position, 'Manager') && 
                       (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance') || str_contains($user->department, 'Admin') || str_contains($user->department, 'Finance'));
        return $user->position === 'Super Admin' || str_contains($user->position, 'Admin') || $user->position === 'A&F Manager' || $isAFManager;
    }

    /**
     * Check if user is eligible to approve as Finance Manager.
     */
    public function canApproveAsFinance($user)
    {
        if ($this->status !== 'pending_admin_finance_approval' || !is_null($this->finance_approved_by)) {
            return false;
        }
        $isAFManager = str_contains($user->position, 'Manager') && 
                       (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance') || str_contains($user->department, 'Admin') || str_contains($user->department, 'Finance'));
        return $user->position === 'Super Admin' || str_contains($user->position, 'Finance') || str_contains($user->position, 'Accounting') || $user->position === 'A&F Manager' || $isAFManager;
    }
}
