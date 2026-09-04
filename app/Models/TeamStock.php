<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'book_id',
        'book_index_id',
        'book_bundle_id',
        'quantity',
    ];

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
