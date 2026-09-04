<?php

namespace App\Models\AdminFinance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequisitionItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function requisition()
    {
        return $this->belongsTo(MaterialRequisition::class, 'material_requisition_id');
    }
}
