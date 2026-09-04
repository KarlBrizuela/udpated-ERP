<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no',
        'sales_order_id',
        'return_date',
        'refund_amount',
        'refund_method',
        'inventory_restored',
        'journal_entry_id',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'refund_amount' => 'decimal:2',
        'inventory_restored' => 'boolean',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
