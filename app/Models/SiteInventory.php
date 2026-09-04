<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteInventory extends Model
{
    protected $table = 'site_inventory';

    protected $fillable = [
        'site_id',
        'book_id',
        'book_index_id',
        'book_bundle_id',
        'quantity',
        'reorder_point',
        'max_stock'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookIndex()
    {
        return $this->belongsTo(BookIndex::class, 'book_index_id');
    }

    public function bookBundle()
    {
        return $this->belongsTo(BookBundle::class, 'book_bundle_id');
    }

    public function getStockStatus()
    {
        if ($this->quantity == 0) {
            return 'out_of_stock';
        } elseif ($this->reorder_point && $this->quantity <= $this->reorder_point) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getInventoryValue()
    {
        if ($this->book) {
            return ($this->book->cost ?? 0) * $this->quantity;
        }
        if ($this->bookIndex && $this->bookIndex->book) {
            return ($this->bookIndex->book->cost ?? 0) * $this->quantity;
        }
        return 0;
    }
}
