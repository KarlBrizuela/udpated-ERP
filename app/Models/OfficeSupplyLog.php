<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeSupplyLog extends Model
{
    use HasFactory;

    protected $table = 'office_supply_logs';

    protected $fillable = [
        'office_supply_id',
        'item_name',
        'supplier_id',
        'added_by',
        'quantity',
        'unit_price',
        'previous_stock',
        'new_stock',
        'notes',
    ];

    /**
     * Get the office supply item.
     */
    public function officeSupply()
    {
        return $this->belongsTo(OfficeSupply::class, 'office_supply_id');
    }

    /**
     * Get the supplier who supplied this stock.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the user who added this stock.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
