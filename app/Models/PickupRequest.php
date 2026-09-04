<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    use HasFactory;

    protected $table = 'pickup_requests';

    protected $fillable = [
        'type',
        'client_name',
        'address',
        'requested_date',
        'driver_id',
        'driver_name',
        'vehicle',
        'items_details',
        'remarks',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason'
    ];

    /**
     * Driver assigned to the request.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    protected $casts = [
        'requested_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * User who created the request.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who approved the request.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * User who rejected the request.
     */
    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
