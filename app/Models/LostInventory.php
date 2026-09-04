<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostInventory extends Model
{
    use HasFactory;

    protected $table = 'lost_inventories';

    protected $fillable = [
        'product_type', // 'book', 'index', 'bundle', 'non_book'
        'book_id',
        'book_index_id',
        'book_bundle_id',
        'quantity',
        'site_id',
        'team_name',
        'reason',
        'user_id',
        'lost_date',
    ];

    protected $casts = [
        'lost_date' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function bookIndex(): BelongsTo
    {
        return $this->belongsTo(BookIndex::class, 'book_index_id');
    }

    public function bookBundle(): BelongsTo
    {
        return $this->belongsTo(BookBundle::class, 'book_bundle_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get display product name
     */
    public function getProductNameAttribute(): string
    {
        if ($this->book_id && $this->book) {
            return $this->book->name;
        }
        if ($this->book_index_id && $this->bookIndex) {
            return $this->bookIndex->title;
        }
        if ($this->book_bundle_id && $this->bookBundle) {
            return $this->bookBundle->bundle_name;
        }
        return 'N/A';
    }

    /**
     * Get display SKU / Article / Code
     */
    public function getSkuIsbnAttribute(): string
    {
        if ($this->book_id && $this->book) {
            return $this->book->sku ?: ($this->book->item_code ?: 'N/A');
        }
        if ($this->book_index_id && $this->bookIndex) {
            return $this->bookIndex->article_number ?: ($this->bookIndex->isbn ?: 'N/A');
        }
        if ($this->book_bundle_id && $this->bookBundle) {
            return $this->bookBundle->bundle_code ?: 'N/A';
        }
        return 'N/A';
    }

    /**
     * Get formatted product type label
     */
    public function getDisplayProductTypeAttribute(): string
    {
        switch ($this->product_type) {
            case 'book': return 'Book';
            case 'index': return 'Index';
            case 'bundle': return 'Bundle';
            case 'non_book': return 'Non-Book';
            default: return ucfirst(str_replace('_', ' ', $this->product_type));
        }
    }
}
