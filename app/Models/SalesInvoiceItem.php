<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'sales_invoice_items';
    
    protected $fillable = [
        'si_id',
        'so_item_id',
        'book_id',
        'quantity',
        'unit_price',
        'amount',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship to Sales Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'si_id');
    }

    /**
     * Relationship to Sales Order Item
     */
    public function soItem()
    {
        return $this->belongsTo(SalesOrderItem::class, 'so_item_id');
    }

    /**
     * Relationship to Book
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
