<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickList extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'pick_list_number',
        'status',
        'prepared_by',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function pickListItems()
    {
        return $this->hasMany(PickListItem::class);
    }

    public function preparedByUser()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Helper methods
    public function getTotalRequestedQuantity()
    {
        return $this->pickListItems->sum('requested_qty');
    }

    public function getTotalPickedQuantity()
    {
        return $this->pickListItems->sum('picked_qty');
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_by' => auth()->id(),
            'completed_at' => now(),
        ]);
    }
}
