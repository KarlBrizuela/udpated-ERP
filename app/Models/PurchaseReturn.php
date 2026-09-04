<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no',
        'supplier_id',
        'supplier_invoice_id',
        'supplier_invoice_no',
        'return_date',
        'refund_amount',
        'inventory_deducted',
        'journal_entry_id',
        'refund_status',
        'notes',
        'prepared_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'refund_amount' => 'decimal:2',
        'inventory_deducted' => 'boolean',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
