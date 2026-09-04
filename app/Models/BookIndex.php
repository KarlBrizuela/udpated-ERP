<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIndex extends Model
{
    protected $table = 'book_indices';

    protected $fillable = [
        'book_id',
        'index_value',
        'custom_name',
        'article',
        'barcode',
        'nbs_barcode',
        'stock',
        'price',
        'mibf_price',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'mibf_price' => 'decimal:2',
    ];

    /**
     * Get the book associated with this index mapping.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Accessor for full display name: custom_name or book name + index_value.
     */
    public function getDisplayNameAttribute()
    {
        if (!empty($this->custom_name)) {
            return $this->custom_name;
        }
        if (!$this->book) {
            return $this->index_value;
        }
        return $this->book->name . ' ' . $this->index_value;
    }

    /**
     * Get the inventory items for this index across different sites.
     */
    public function inventory()
    {
        return $this->hasMany(SiteInventory::class, 'book_index_id');
    }

    /**
     * Get the stock transfers for this index.
     */
    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'book_index_id');
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
