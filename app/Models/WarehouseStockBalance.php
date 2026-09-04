<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'inventory_category_item_id',
        'quantity',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryCategoryItem::class, 'inventory_category_item_id');
    }
}
