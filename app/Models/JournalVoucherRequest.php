<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'jv_number',
        'client_name',
        'date',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'reason',
        'category',
        'total_amount',
        'documents',
        'supporting_documents',
        'accounting_remarks',
        'manager_approved_by',
        'manager_approved_at',
        'rejected_by',
        'rejected_at'
    ];

    public function items()
    {
        return $this->hasMany(JournalVoucherItem::class, 'jv_request_id');
    }

    public function requestor()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
