<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_fixed_asset_id',
        'maintenance_date',
        'title',
        'technician',
        'repair_cost',
        'details',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(ProductionFixedAsset::class, 'production_fixed_asset_id');
    }
}
