<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookBundle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'mibf_price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mibf_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the books included in this bundle.
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_bundle_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the inventory items for this bundle across different sites.
     */
    public function inventory()
    {
        return $this->hasMany(SiteInventory::class, 'book_bundle_id');
    }

    /**
     * Get the stock transfers for this bundle.
     */
    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'book_bundle_id');
    }

    /**
     * Accessor for stock at Main Warehouse site only.
     */
    public function getMainStockAttribute()
    {
        $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
        if ($mainWarehouse) {
            $siteInv = $this->inventory()->where('site_id', $mainWarehouse->id)->first();
            if ($siteInv) {
                return (int)$siteInv->quantity;
            }
        }
        return (int)$this->stock;
    }
}
