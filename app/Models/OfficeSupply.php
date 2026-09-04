<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeSupply extends Model
{
    use HasFactory;

    protected $table = 'office_supplies';

    protected $fillable = [
        'item_name',
        'item_price',
        'items_stock',
        'unit',
    ];

    /**
     * Get the logs/history of stock additions.
     */
    public function logs()
    {
        return $this->hasMany(OfficeSupplyLog::class, 'office_supply_id')->orderBy('created_at', 'desc');
    }
}
