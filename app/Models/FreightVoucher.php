<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'fv_number',
        'date',
        'supplier_id',
        'pay_to',
        'approved_by',
        'received_by',
        'status',
        'journal_entry_id',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(FreightVoucherItem::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
