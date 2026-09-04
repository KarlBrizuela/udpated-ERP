<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamStockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_stock_transfer_id',
        'book_id',
        'book_index_id',
        'book_bundle_id',
        'quantity',
        'lost_quantity',
        'picked_qty',
        'packed_qty',
        'status',
        'notes',
        'picked_date',
        'packed_date',
    ];

    public function transfer()
    {
        return $this->belongsTo(TeamStockTransfer::class, 'team_stock_transfer_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookIndex()
    {
        return $this->belongsTo(BookIndex::class);
    }

    public function bookBundle()
    {
        return $this->belongsTo(BookBundle::class);
    }

    protected $appends = [
        'product_name',
        'item_type',
    ];

    public function getItemTypeAttribute()
    {
        if ($this->book_bundle_id || $this->bookBundle) {
            return 'Bundle';
        }
        if ($this->book_index_id || $this->bookIndex) {
            return 'Index';
        }
        if ($this->book_id || $this->book) {
            return 'Book';
        }
        return 'Book';
    }

    public function getProductNameAttribute()
    {
        if ($this->book_index_id && $this->bookIndex) {
            return $this->bookIndex->display_name;
        }
        if ($this->book_id && $this->book) {
            return $this->book->name;
        }
        if ($this->book_bundle_id && $this->bookBundle) {
            return $this->bookBundle->name;
        }
        return 'Unknown Product';
    }
}
