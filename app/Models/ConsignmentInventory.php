<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'customer_id',
        'team_name',
        'book_id',
        'book_index_id',
        'book_bundle_id',
        'quantity',
        'status',
        'notes',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function bookIndex()
    {
        return $this->belongsTo(BookIndex::class, 'book_index_id');
    }

    public function bookBundle()
    {
        return $this->belongsTo(BookBundle::class, 'book_bundle_id');
    }
}
