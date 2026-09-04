<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentSettlement extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'amount', 'total_qty', 'settled_at'];

    protected $casts = [
        'settled_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function owner()
    {
        return $this->belongsTo(ConsignmentOwner::class, 'owner_id');
    }
}
