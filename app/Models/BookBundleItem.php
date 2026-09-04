<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookBundleItem extends Pivot
{
    protected $table = 'book_bundle_items';

    protected $fillable = [
        'book_bundle_id',
        'book_id',
        'quantity',
    ];

    public function bundle()
    {
        return $this->belongsTo(BookBundle::class, 'book_bundle_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
