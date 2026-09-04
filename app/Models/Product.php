<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'item_id',
        'name',
        'price',
        'category',
        'image',
        'sales_description',
        'asset_account',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the master book registry entry.
     */
    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the master item registry entry.
     */
    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the source (book or item) for this product.
     */
    public function getSourceAttribute()
    {
        return $this->book ?? $this->item;
    }

    /**
     * Delegate stock access to the master record (book or item).
     */
    public function getStockAttribute()
    {
        return $this->source->stock ?? 0;
    }

    /**
     * Delegate SKU access to master record (book or item).
     */
    public function getSkuAttribute()
    {
        return $this->source->sku ?? 'N/A';
    }
}
