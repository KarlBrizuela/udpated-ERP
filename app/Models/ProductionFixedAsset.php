<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductionFixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'purchase_date',
        'supplier',
        'purchase_price',
        'warranty_expiry',
        'serial_number',
        'useful_life_years',
        'salvage_value',
        'accumulated_depreciation',
        'total_repair_cost',
        'current_value',
        'status',
        'location',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function maintenanceLogs()
    {
        return $this->hasMany(AssetMaintenanceLog::class, 'production_fixed_asset_id')->latest('maintenance_date');
    }

    public function calculateDepreciation()
    {
        $cost = (float) $this->purchase_price;
        $salvage = (float) $this->salvage_value;
        $usefulYears = max(1, (int) $this->useful_life_years);

        if (!$this->purchase_date) {
            $this->accumulated_depreciation = 0;
            $this->current_value = $cost;
            return $this;
        }

        $purchaseDate = Carbon::parse($this->purchase_date);
        $now = Carbon::now();
        $yearsElapsed = max(0, $purchaseDate->diffInDays($now) / 365.25);

        $annualDepreciation = ($cost - $salvage) / $usefulYears;
        $accumulated = min($cost - $salvage, $annualDepreciation * $yearsElapsed);
        $currentValue = max($salvage, $cost - $accumulated);

        $this->accumulated_depreciation = round($accumulated, 2);
        $this->current_value = round($currentValue, 2);

        return $this;
    }

    public function recalculateRepairCosts()
    {
        $this->total_repair_cost = round($this->maintenanceLogs()->sum('repair_cost'), 2);
        return $this;
    }
}
