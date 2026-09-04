<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCategoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'subcategory',
        'unit_of_measure',
        'unit_cost',
        'reorder_point',
        'description',
    ];

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStockBalance::class, 'inventory_category_item_id');
    }

    public function getTotalStockAttribute()
    {
        return $this->warehouseStocks()->sum('quantity');
    }
}
