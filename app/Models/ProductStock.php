<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $fillable = [
        'book_id',
        'location',
        'quantity',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
