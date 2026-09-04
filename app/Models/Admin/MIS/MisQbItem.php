<?php

namespace App\Models\Admin\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MisQbItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(MisQbRequest::class, 'qb_req_id');
    }
}
