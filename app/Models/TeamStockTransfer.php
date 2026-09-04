<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamStockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'transfer_type',
        'team_name',
        'transferred_by',
        'notes',
        'remarks',
        'status',
    ];

    public function transferredByUser()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function items()
    {
        return $this->hasMany(TeamStockTransferItem::class);
    }
}
