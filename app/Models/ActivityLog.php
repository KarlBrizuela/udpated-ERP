<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'status',
        'description',
        'ip_address',
        'reference_type',
        'reference_id',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
