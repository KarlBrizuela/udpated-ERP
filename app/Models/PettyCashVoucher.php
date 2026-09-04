<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashVoucher extends Model
{
    protected $fillable = [
        'pcv_number',
        'type',
        'date',
        'pay_to',
        'approved_by',
        'received_by',
        'status',
        'journal_entry_id',
        'created_by',
        'proof_attachment',
    ];

    public function items()
    {
        return $this->hasMany(PettyCashVoucherItem::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
