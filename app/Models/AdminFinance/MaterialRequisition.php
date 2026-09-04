<?php

namespace App\Models\AdminFinance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequisition extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(MaterialRequisitionItem::class, 'material_requisition_id');
    }
}
