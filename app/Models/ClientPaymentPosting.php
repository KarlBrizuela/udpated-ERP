<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPaymentPosting extends Model
{
    protected $fillable = [
        'date',
        'status',
        'prepared_by',
    ];

    public function items()
    {
        return $this->hasMany(ClientPaymentPostingItem::class, 'posting_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
