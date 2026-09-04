<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category',
        'description',
        'stock',
        'reorder_point',
        'max_stock',
        'unit',
        'cost',
        'cogs_account',
        'purchase_description'
    ];

    /**
     * Get the product listing for this item (if listed on POS)
     */
    public function product()
    {
        return $this->hasOne(Product::class);
    }
}
