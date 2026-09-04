<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pick_list_id',
        'sales_order_item_id',
        'requested_qty',
        'picked_qty',
        'status',
        'notes',
    ];

    protected $casts = [
        'requested_qty' => 'decimal:2',
        'picked_qty' => 'decimal:2',
    ];

    // Relationships
    public function pickList()
    {
        return $this->belongsTo(PickList::class);
    }

    public function salesOrderItem()
    {
        return $this->belongsTo(SalesOrderItem::class);
    }
}
