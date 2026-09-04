<?php

namespace App\Models\Admin\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MisQbRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'qb_req_id';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(MisQbItem::class, 'qb_req_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
