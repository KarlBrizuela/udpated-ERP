<?php

namespace App\Models\Admin\GSD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $primaryKey = 'asset_id';

    protected $fillable = [
        'property_code',
        'category',
        'description',
        'acquisition_date',
        'department',
        'checked_by',
        'status',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($asset) {
            if (empty($asset->property_code)) {
                $year = date('Y');
                $prefix = "PROP-{$year}-";
                
                $latestAsset = static::where('property_code', 'like', $prefix . '%')
                    ->orderBy('property_code', 'desc')
                    ->first();

                $sequence = 1;
                if ($latestAsset) {
                    $lastSequence = (int) substr($latestAsset->property_code, -3);
                    $sequence = $lastSequence + 1;
                }

                $asset->property_code = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
