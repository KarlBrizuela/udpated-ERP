<?php

namespace App\Models\Admin\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MisServiceRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'service_req_id';

    protected $fillable = [
        'user_id',
        'module',
        'requestor_name',
        'date',
        'nature_of_request',
        'department',
        'status',
        'approved_by',
        'completed_by',
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
