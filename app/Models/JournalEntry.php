<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_no',
        'entry_type',
        'date',
        'reference',
        'memo',
        'currency',
        'exchange_rate',
        'created_by',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
